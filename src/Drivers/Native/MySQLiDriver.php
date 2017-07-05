<?php
/**
 * Class MySQLiDriver
 *
 * @filesource   MySQLiDriver.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers\MySQLi
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers\Native;

use chillerlan\Database\Drivers\{DriverAbstract, DriverException, DriverInterface};
use chillerlan\Database\Result;
use mysqli;

class MySQLiDriver extends DriverAbstract{

	/**
	 * Holds the database resource object
	 *
	 * @var mysqli
	 */
	protected $db;

	/**
	 * @inheritdoc
	 */
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
				(int)$this->options->port,
				$this->options->socket
			);

			/**
			 * @see https://mathiasbynens.be/notes/mysql-utf8mb4 How to support full Unicode in MySQLStatement
			 */
			$this->db->set_charset($this->options->mysql_charset);

			return $this;
		}
		catch(\Exception $e){
			throw new DriverException('db error: [MySQLiDriver]: '.$e->getMessage());
		}

	}

	/**
	 * @inheritdoc
	 */
	public function disconnect():bool{

		if($this->db instanceof mysqli){
			// prevent Warning: mysqli::close(): Couldn't fetch mysqli
			$r = $this->db->close();

			if($r){
				$this->db = null;
			}

			return $r;
		}

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function getClientInfo():string{
		return $this->db->client_info;
	}

	/**
	 * @inheritdoc
	 */
	public function getServerInfo():string{
		return $this->db->server_info;
	}

	/**
	 * @inheritdoc
	 */
	public function escape($data){
		return $this->db->real_escape_string($data);
	}

	/**
	 * @inheritdoc
	 */
	protected function raw_query(string $sql, string $index = null, bool $assoc = true){
		$result = $this->db->query($sql);

		if(is_bool($result)){
			return $result;
		}

		$r = $this->getResult([$result, 'fetch_'.($assoc ? 'assoc' : 'row')], [], $index, $assoc);

		$result->free();

		return $r;
	}

	/**
	 * @inheritdoc
	 */
	protected function prepared_query(string $sql, array $values = [], string $index = null, bool $assoc = true){
		$stmt = $this->db->stmt_init();
		$stmt->prepare($sql);

		if(count($values) > 0){
			call_user_func_array([$stmt, 'bind_param'], $this->getReferences($values));
		}

		$stmt->execute();

		$result = $stmt->result_metadata();

		if(is_bool($result)){
			return true; // @todo: returning $result causes trouble on prepared INSERT first line. why???
		}

		// get the columns and their references
		// http://php.net/manual/mysqli-stmt.bind-result.php
		$cols = [];
		$refs = [];

		foreach($result->fetch_fields() as $k => &$field){
			$refs[] = &$cols[$assoc ? $field->name : $k];
		}

		call_user_func_array([$stmt, 'bind_result'], $refs);

		// fetch the data
		$output = new Result(null, $this->options->convert_encoding_src, $this->options->convert_encoding_dest);
		$i      = 0;

		while($stmt->fetch()){
			$row = [];
			$key = $i;

			foreach($cols as $field => &$data){
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

		return $i === 0 ? true : $output;

/*
		$this->addStats([
			'affected_rows' => $stmt->affected_rows,
			'error'         => $stmt->error_list,
			'insert_id'     => $stmt->insert_id,
			'sql'           => $sql,
			'values'        => $values,
			'types'         => $types,
			'index'         => $index,
			'assoc'         => $assoc,
		]);
*/
	}

	/**
	 * @inheritdoc
	 */
	protected function multi_query(string $sql, array $values){
		$stmt = $this->db->stmt_init();
		$stmt->prepare($sql);

		foreach($values as $row){
			call_user_func_array([$stmt, 'bind_param'], $this->getReferences($row));

			$stmt->execute();
		}

		$stmt->close();

		return true;
	}

	/**
	 * @inheritdoc
	 */
	protected function multi_callback_query(string $sql, array $data, $callback){
		$stmt = $this->db->stmt_init();
		$stmt->prepare($sql);

		foreach($data as $k => $row){
			if($row = call_user_func_array($callback, [$row, $k])){
				call_user_func_array([$stmt, 'bind_param'], $this->getReferences($row));

				$stmt->execute();
			}
		}

		$stmt->close();

		return true;
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

			switch(gettype($field)){
				case 'integer': $types[] = 'i'; break;
				case 'double' : $types[] = 'd'; break; // @codeCoverageIgnore
				default:        $types[] = 's'; break;
			}

			$references[] = &$field;
		}

		array_unshift($references, implode('', $types));

		return $references;
	}

}
