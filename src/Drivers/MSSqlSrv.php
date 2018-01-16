<?php
/**
 * Class MSSqlSrv
 *
 * @filesource   MSSqlSrv.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Dialects\MSSQL;

/**
 * @property resource $db
 */
class MSSqlSrv extends DriverAbstract{

	protected $dialect = MSSQL::class;

	/** @inheritdoc */
	protected function raw_query(string $sql, string $index = null, bool $assoc = null){
		return $this->__getResult(sqlsrv_query($this->db, $sql), $index, $assoc);
	}

	/** @inheritdoc */
	protected function prepared_query(string $sql, array $values = null, string $index = null, bool $assoc = null){
		return $this->__getResult(sqlsrv_query($this->db, $sql, $values), $index, $assoc);
	}

	/** @inheritdoc */
	protected function multi_query(string $sql, array $values){
		$r = [];

		// @todo: sqlsrv_prepare/sqlsrv_execute
		foreach($values as $row){
			$r[] = $this->prepared_query($sql, $row);
		}

		foreach($r as $result){

			if(!$result){
				return false;
			}

		}

		return true;
	}

	/** @inheritdoc */
	protected function multi_callback_query(string $sql, iterable $data, $callback){
		$r = [];

		// @todo: sqlsrv_prepare/sqlsrv_execute
		foreach($data as $i => $row){
			$r[] = $this->prepared_query($sql, call_user_func_array($callback, [$row, $i]));
		}

		foreach($r as $result){

			if(!$result){
				return false;
			}

		}

		return true;
	}

	/**
	 * @param             $result
	 * @param string|null $index
	 * @param bool        $assoc
	 *
	 * @return bool|\chillerlan\Database\Result
	 */
	protected function __getResult($result, string $index = null, bool $assoc = null){

		if(is_bool($result)){
			return $result;
		}

		$r = parent::getResult('sqlsrv_fetch_array', [$result, ($assoc ?? true) ? SQLSRV_FETCH_ASSOC : SQLSRV_FETCH_NUMERIC], $index, $assoc);

		sqlsrv_free_stmt($result);

		return $r;
	}

	/** @inheritdoc */
	public function connect():DriverInterface{

		if(gettype($this->db) === 'resource'){
			return $this;
		}

		$options = [];

		$host = $this->options->host;

		if(is_numeric($this->options->port)){
			$host .= ', '.$this->options->port;
		}

		if($this->options->database){
			$options['Database'] = $this->options->database;
		}

		if($this->options->username){
			$options['UID'] = $this->options->username;
		}

		if($this->options->password){
			$options['PWD'] = $this->options->password;
		}

		if($this->options->mssql_timeout){
			$options['LoginTimeout'] = $this->options->mssql_timeout;
		}

		if($this->options->mssql_charset){
			$options['CharacterSet'] = $this->options->mssql_charset;
		}

		if($this->options->mssql_encrypt){
			$options['Encrypt'] = $this->options->mssql_encrypt;
		}

		$this->db = sqlsrv_connect($host, $options);

		if(!$this->db){
			$errors = sqlsrv_errors();

			if(is_array($errors) && isset($errors['SQLSTATE'], $errors['code'], $errors['message'])){
				throw new DriverException('db error: [MSSqlSrv]: [SQLSTATE '.$errors['SQLSTATE'].'] ('.$errors['code'].') '.$errors['message']);
			}

			throw new DriverException('db error: [MSSqlSrv]: could not connect: '.print_r($errors, true));
		}

		return $this;
	}

	/** @inheritdoc */
	public function disconnect():bool{

		if(gettype($this->db) === 'resource'){
			return sqlsrv_close($this->db);
		}

		return true;
	}

	/** @inheritdoc */
	public function getClientInfo():string{

		if(gettype($this->db) === 'resource'){
			$info = sqlsrv_client_info($this->db);

			return $info['DriverVer'].' - '.$info['ExtensionVer'].' - '.$info['DriverODBCVer'];
		}

		return 'disconnected, no info available';
	}

	/** @inheritdoc */
	public function getServerInfo():?string{

		if(gettype($this->db) === 'resource'){
			$info = sqlsrv_server_info($this->db);

			return PHP_OS.' - '.$info['SQLServerVersion'].' - '.$info['SQLServerName'].'/'.$info['CurrentDatabase'];
		}

		return 'disconnected, no info available';
	}

	/** @inheritdoc */
	public function escape(string $data):string {
		return $data;
	}

}
