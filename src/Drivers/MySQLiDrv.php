<?php
/**
 * Class MySQLiDrv
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
 */
declare(strict_types=1);

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Dialects\MySQL;
use chillerlan\Database\Result;
use Closure, Throwable, mysqli;
use function array_unshift, count, gettype, implode, is_array, is_bool, mysqli_init;
use const MYSQLI_OPT_CONNECT_TIMEOUT;

/**
 *
 */
final class MySQLiDrv extends DriverAbstract{

	protected const DIALECT = MySQL::class;

	/**
	 * Holds the database resource object
	 *
	 * @var mysqli|null
	 */
	private mysqli|null $db = null;

	public function connect():DriverInterface{

		if($this->db instanceof mysqli){
			return $this;
		}

		try{
			$this->db = mysqli_init();

			$this->db->options(MYSQLI_OPT_CONNECT_TIMEOUT, $this->options->mysqli_timeout);

			// @codeCoverageIgnoreStart
			if($this->options->use_ssl){
				$this->db->ssl_set(
					$this->options->ssl_key,
					$this->options->ssl_cert,
					$this->options->ssl_ca,
					$this->options->ssl_capath,
					$this->options->ssl_cipher
				);
			}
			// @codeCoverageIgnoreEnd

			$this->db->real_connect(
				$this->options->host,
				$this->options->username,
				$this->options->password,
				$this->options->database,
				!empty($this->options->port) ? (int)$this->options->port : null,
				$this->options->socket
			);

			/**
			 * @see https://mathiasbynens.be/notes/mysql-utf8mb4 How to support full Unicode in MySQL
			 */
			$this->db->set_charset($this->options->mysql_charset);

			return $this;
		}
		catch(Throwable $e){
			throw new DriverException('db error: [MySQLiDrv]: '.$e->getMessage());
		}

	}

	public function disconnect():bool{

		if($this->db instanceof mysqli){
			$this->db->close();

			$this->db = null;
		}

		return true;
	}

	public function getDBResource():mysqli|null{
		return $this->db;
	}

	public function getClientInfo():string{

		if(!$this->db instanceof mysqli){
			return 'disconnected, no info available';
		}

		return $this->db->client_info;
	}

	public function getServerInfo():string{

		if(!$this->db instanceof mysqli){
			return 'disconnected, no info available';
		}

		return $this->db->server_info;
	}

	protected function raw_query(string $sql, string|null $index = null, bool|null $assoc = null):Result{
		$result = $this->db->query($sql);

		if(is_bool($result)){

			if($this->db->errno !== 0){
				throw new DriverException($this->db->error, $this->db->errno);
			}

			return new Result(null, null, null, true, $result);
		}

		$r = $this->getResult((($assoc ?? true) ? $result->fetch_assoc(...) : $result->fetch_row(...)), [], $index, $assoc);

		$result->free();

		return $r;
	}

	protected function prepared_query(string $sql, array|null $values = null, string|null $index = null, bool|null $assoc = null):Result{
		$values ??= [];
		$assoc  ??= true;
		$stmt     = $this->db->stmt_init();

		$stmt->prepare($sql);
		$this->stmtError($this->db->errno, $this->db->error);

		if(count($values) > 0){
			/** @phan-suppress-next-line PhanParamTooFewInternalUnpack, PhanTypeNonVarPassByRef */
			$stmt->bind_param(...$this->getReferences($values));
		}

		$stmt->execute();

		$result = $stmt->result_metadata();

		if(is_bool($result)){
			// https://www.php.net/manual/mysqli-stmt.result-metadata.php#97338
			// the query did not produce a result, everything ok.
			return new Result(null, null, null, true, true);
		}

		// get the columns and their references
		// http://php.net/manual/mysqli-stmt.bind-result.php
		$cols = [];
		$refs = [];

		foreach($result->fetch_fields() as $k => $field){
			/** @phan-suppress-next-line PhanTypeInvalidDimOffset */
			$refs[] = &$cols[$assoc ? $field->name : $k];
		}

		/** @phan-suppress-next-line PhanParamTooFewInternalUnpack */
		$stmt->bind_result(...$refs);

		// fetch the data
		$output = new Result(null, $this->convert_encoding_src, $this->convert_encoding_dest);
		$i      = 0;

		while($stmt->fetch()){
			$row = [];
			$key = $i;

			/** @phan-suppress-next-line PhanEmptyForeach */
			foreach($cols as $field => $data){
				$row[$field] = $data;
			}

			if($assoc && !empty($index)){
				$key = $row[$index] ?? $i;
			}

			$output[$key] = $row;
			$i++;
		}

		// KTHXBYE!
		$stmt->free_result();
		$stmt->close();

		return $output;
	}

	protected function multi_query(string $sql, array $values):bool{
		$stmt = $this->db->stmt_init();
		$stmt->prepare($sql);
		$this->stmtError($this->db->errno, $this->db->error);

		foreach($values as $row){
			/** @phan-suppress-next-line PhanParamTooFewInternalUnpack, PhanTypeNonVarPassByRef */
			$stmt->bind_param(...$this->getReferences($row));

			$stmt->execute();
		}

		$stmt->close();

		return true;
	}

	protected function multi_callback_query(string $sql, array $data, Closure $callback):bool{
		$stmt = $this->db->stmt_init();
		$stmt->prepare($sql);
		$this->stmtError($this->db->errno, $this->db->error);

		foreach($data as $k => $row){
			$row = $callback($row, $k);

			if(is_array($row) && !empty($row)){
				/** @phan-suppress-next-line PhanParamTooFewInternalUnpack, PhanTypeNonVarPassByRef */
				$stmt->bind_param(...$this->getReferences($row));

				$stmt->execute();
			}
		}

		$stmt->close();

		return true;
	}

	/**
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	private function stmtError(int $errno, string $errstr):void{

		if($errno !== 0){
			throw new DriverException($errstr, $errno);
		}

	}

	/**
	 * Copies an array to an array of referenced values
	 *
	 * @see http://php.net/manual/mysqli-stmt.bind-param.php
	 */
	private function getReferences(array $row):array{
		$references = [];
		$types      = [];

		foreach($row as &$field){
			$type = gettype($field);

			if($type === 'integer'){
				$types[] = 'i';
			}
			elseif($type === 'double'){
				$types[] = 'd';
			}
			else{
				$types[] = 's';
			}

			$references[] = &$field;
		}

		array_unshift($references, implode('', $types));

		return $references;
	}

}
