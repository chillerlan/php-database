<?php
/**
 * Class MSSqlSrv
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

use chillerlan\Database\Dialects\MSSQL;
use chillerlan\Database\Result;
use Closure;
use function array_values, gettype, implode, is_bool, is_numeric, sprintf, sodium_bin2hex, sqlsrv_client_info,
	sqlsrv_close, sqlsrv_connect, sqlsrv_errors, sqlsrv_fetch_array, sqlsrv_free_stmt, sqlsrv_query, sqlsrv_server_info;
use const PHP_OS, SQLSRV_FETCH_ASSOC, SQLSRV_FETCH_NUMERIC;

/**
 * Microsoft SQL Server (native)
 *
 * @link https://github.com/Microsoft/msphpsql
 */
final class MSSqlSrv extends DriverAbstract{

	protected const DIALECT = MSSQL::class;

	/**
	 * Holds the database resource object
	 *
	 * @var resource|null
	 */
	private $db = null;

	public function connect():DriverInterface{

		if(gettype($this->db) === 'resource'){
			return $this;
		}

		$options = [];
		$host    = $this->options->host;

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
			throw new DriverException('db error: [MSSqlSrv]: could not connect: '.$this->parseErrors(sqlsrv_errors() ?? []));
		}

		return $this;
	}

	public function disconnect():bool{

		if(gettype($this->db) === 'resource'){
			return sqlsrv_close($this->db);
		}

		return true;
	}

	public function getDBResource():mixed{
		return $this->db;
	}

	public function getClientInfo():string{

		if(gettype($this->db) === 'resource'){
			$info = sqlsrv_client_info($this->db);

			return $info['DriverVer'].' - '.$info['ExtensionVer'].' - '.$info['DriverODBCVer'];
		}

		return 'disconnected, no info available';
	}

	public function getServerInfo():string{

		if(gettype($this->db) === 'resource'){
			$info = sqlsrv_server_info($this->db);

			return PHP_OS.' - '.$info['SQLServerVersion'].' - '.$info['SQLServerName'].'/'.$info['CurrentDatabase'];
		}

		return 'disconnected, no info available';
	}

	/**
	 * @see https://docs.microsoft.com/sql/t-sql/data-types/constants-transact-sql?view=sql-server-ver15
	 */
	protected function escape_string(string $string):string{

		if($string === ''){
			return "''";
		}

		// convert to hex literal, sql server only accepts the 0x... format
		return '0x'.sodium_bin2hex($string);
	}

	protected function raw_query(string $sql, string|null $index = null, bool|null $assoc = null):Result{
		return $this->get_result(sqlsrv_query($this->db, $sql), $index, $assoc);
	}

	protected function prepared_query(string $sql, array|null $values = null, string|null $index = null, bool|null $assoc = null):Result{

		// [SQLSTATE IMSSP] (-57) String keys are not allowed in parameters arrays.
		if($values !== null){
			$values = array_values($values);
		}

		return $this->get_result(sqlsrv_query($this->db, $sql, $values ?? []), $index, $assoc);
	}

	protected function multi_query(string $sql, array $values):bool{
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

	protected function multi_callback_query(string $sql, array $data, Closure $callback):bool{
		$r = [];

		// @todo: sqlsrv_prepare/sqlsrv_execute
		foreach($data as $i => $row){
			$r[] = $this->prepared_query($sql, $callback($row, $i));
		}

		foreach($r as $result){

			if(!$result){
				return false;
			}

		}

		return true;
	}

	/**
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	private function get_result($result, string|null $index = null, bool|null $assoc = null):Result{

		if(is_bool($result)){
			$errors = sqlsrv_errors();

			if(!$result && !empty($errors)){
				throw new DriverException('sql error: '.$this->parseErrors($errors));
			}

			return new Result(null, null, null, true, $result);
		}

		$r = parent::getResult(
			sqlsrv_fetch_array(...),
			/** @phan-suppress-next-line PhanUndeclaredConstant */
			[$result, ($assoc ?? true) ? SQLSRV_FETCH_ASSOC : SQLSRV_FETCH_NUMERIC],
			$index,
			$assoc
		);

		sqlsrv_free_stmt($result);

		return $r;
	}

	private function parseErrors(array $errors):string{
		$tpl = '[SQLSTATE %s] (%s) %s';

		if(isset($errors['SQLSTATE'], $errors['code'], $errors['message'])){
			return sprintf($tpl, $errors['SQLSTATE'], $errors['code'], $errors['message']);
		}

		$msg = [];

		foreach($errors as $error){
			$msg[] = sprintf($tpl, $error['SQLSTATE'], $error['code'], $error['message']);
		}

		return implode("\n", $msg);
	}

}
