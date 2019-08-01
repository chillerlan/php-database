<?php
/**
 * Class MySQLiDrv
 *
 * @filesource   MySQLphp
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Dialects\MySQL;
use chillerlan\Database\Result;
use Exception;
use mysqli;

/**
 * @property mysqli $db
 */
class MySQLiDrv extends DriverAbstract{

	protected $dialect = MySQL::class;

	/** @inheritdoc */
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
		catch(Exception $e){
			throw new DriverException('db error: [MySQLiDrv]: '.$e->getMessage());
		}

	}

	/** @inheritdoc */
	public function disconnect():bool{

		if($this->db instanceof mysqli){
			$this->db->close();

			$this->db = null;
		}

		return true;
	}

	/** @inheritdoc */
	public function getClientInfo():string{
		return $this->db->client_info;
	}

	/** @inheritdoc */
	public function getServerInfo():string{
		return $this->db->server_info;
	}

	/** @inheritdoc */
	protected function __escape(string $data):string{
		return '\''.$this->db->real_escape_string($data).'\''; // emulate PDO
	}

	/** @inheritdoc */
	protected function raw_query(string $sql, string $index = null, bool $assoc = null){
		$result = $this->db->query($sql);

		if(is_bool($result)){

			if($this->db->errno !== 0 || !$result){
				throw new DriverException($this->db->error, $this->db->errno);
			}

			return $result; // @codeCoverageIgnore
		}

		$r = $this->getResult([$result, 'fetch_'.(($assoc ?? true) ? 'assoc' : 'row')], [], $index, $assoc);

		$result->free();

		return $r;
	}

	/** @inheritdoc */
	protected function prepared_query(string $sql, array $values = null, string $index = null, bool $assoc = null){
		$assoc = $assoc ?? true;
		$stmt = $this->db->stmt_init();
		$stmt->prepare($sql);
		$this->stmtError($this->db->errno, $this->db->error);

		if(count($values) > 0){
			call_user_func_array([$stmt, 'bind_param'], $this->getReferences($values));
		}

		$stmt->execute();

		$result = $stmt->result_metadata();

		if(is_bool($result)){
			// https://www.php.net/manual/mysqli-stmt.result-metadata.php#97338
			// the query did not produce a result, everything ok.
			return true;
		}

		// get the columns and their references
		// http://php.net/manual/mysqli-stmt.bind-result.php
		$cols = [];
		$refs = [];

		foreach($result->fetch_fields() as $k => $field){
			$refs[] = &$cols[$assoc ? $field->name : $k];
		}

		call_user_func_array([$stmt, 'bind_result'], $refs);

		// fetch the data
		$output = new Result(null, $this->convert_encoding_src, $this->convert_encoding_dest);
		$i      = 0;

		while($stmt->fetch()){
			$row = [];
			$key = $i;

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

		return $i === 0 ? true : $output; // @todo: return proper Result object in all cases
	}

	/** @inheritdoc */
	protected function multi_query(string $sql, array $values){
		$stmt = $this->db->stmt_init();
		$stmt->prepare($sql);
		$this->stmtError($this->db->errno, $this->db->error);

		foreach($values as $row){
			call_user_func_array([$stmt, 'bind_param'], $this->getReferences($row));

			$stmt->execute();
		}

		$stmt->close();

		return true;
	}

	/** @inheritdoc */
	protected function multi_callback_query(string $sql, iterable $data, $callback){
		$stmt = $this->db->stmt_init();
		$stmt->prepare($sql);
		$this->stmtError($this->db->errno, $this->db->error);

		foreach($data as $k => $row){
			$row = call_user_func_array($callback, [$row, $k]);

			if($row !== false && !empty($row)){
				call_user_func_array([$stmt, 'bind_param'], $this->getReferences($row));

				$stmt->execute();
			}
		}

		$stmt->close();

		return true;
	}

	/**
	 * @param int    $errno
	 * @param string $errstr
	 *
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	protected function stmtError(int $errno, string $errstr):void{

		if($errno !== 0){
			throw new DriverException($errstr, $errno);
		}

	}

	/**
	 * Copies an array to an array of referenced values
	 *
	 * @param array $row
	 *
	 * @return array
	 * @see http://php.net/manual/mysqli-stmt.bind-param.php
	 */
	protected function getReferences(array $row){
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
