<?php
/**
 * Class PostgreSQLDriver
 *
 * @filesource   PostgreSQLDriver.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers\Native
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers\Native;

use chillerlan\Database\Drivers\{DriverAbstract, DriverException, DriverInterface};
use chillerlan\Database\Result;

class PostgreSQLDriver extends DriverAbstract{

	/**
	 * Holds the database resource object
	 *
	 * @var resource
	 */
	protected $db;

	/**
	 * @inheritdoc
	 */
	public function connect():DriverInterface{

		if(gettype($this->db) === 'resource'){
			return $this;
		}

		// i am an ugly duckling. fix me please.

		$options = [
			'--client_encoding='.$this->options->pgsql_charset,
		];

		$conn_str = [
			'host=\''.$this->options->host.'\'',
			'port=\''.(int)$this->options->port.'\'',
			'dbname=\''.$this->options->database.'\'',
			'user=\''.$this->options->username.'\'',
			'password=\''.$this->options->password.'\'',
			'options=\''.implode(' ', $options).'\'',
		];

		try{
			$this->db = pg_connect(implode(' ', $conn_str));

			return $this;
		}
		catch(\Exception $e){
			throw new DriverException('db error: [PostgreSQLDriver]: '.$e->getMessage());
		}

	}

	/**
	 * @inheritdoc
	 */
	public function disconnect():bool{

		if(gettype($this->db) === 'resource'){
			return pg_close($this->db);
		}

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function getClientInfo():string{
		$ver = pg_version($this->db);

		return 'PostgreSQL '.$ver['client'].' ('.$ver['client_encoding'].')';
	}

	/**
	 * @inheritdoc
	 */
	public function getServerInfo():string{
		$ver = pg_version($this->db);

		return 'PostgreSQL '.$ver['server'].' ('.$ver['server_encoding'].', date style: '.$ver['DateStyle'].', time zone: '.$ver['TimeZone'].')';
	}

	/**
	 * @inheritdoc
	 */
	public function escape($data){
		return pg_escape_string($this->db, $data);
	}

	/**
	 * @param             $result
	 * @param string|null $index
	 * @param bool        $assoc
	 *
	 * @return bool|\chillerlan\Database\Result
	 */
	protected function __getResult($result, string $index = null, bool $assoc = true){

		if(is_bool($result)){
			return $result; // @codeCoverageIgnore
		}

		$out = new Result(null, $this->options->convert_encoding_src, $this->options->convert_encoding_dest);
		$i   = 0;

		while($row = call_user_func_array($assoc ? 'pg_fetch_assoc' : 'pg_fetch_row', [$result])){
			$key = $i;

			$j = 0;
			foreach($row as $k => $item){
				// https://gitter.im/arenanet/api-cdi?at=594326ba31f589c64fafe554
				$t = pg_field_type($result, $j);

				switch(true){
					case $t === 'bool':
						$row[$k] = $item === 't'; break;
					case in_array($t, ['int2', 'int4', 'int8']):
						$row[$k] = (int)$item; break;
					case in_array($t, ['float4', 'float8']):
						$row[$k] = (float)$item; break;
				}

				$j++;
			}

			if($assoc && !empty($index)){
				$key = $row[$index];
			}

			$out[$key] = $row;
			$i++;
		}

		pg_free_result($result);

		return $i === 0 ? true : $out;
	}

	/**
	 * @inheritdoc
	 */
	protected function raw_query(string $sql, string $index = null, bool $assoc = true){
		return $this->__getResult(pg_query($sql), $index, $assoc);
	}

	/**
	 * @inheritdoc
	 */
	protected function prepared_query(string $sql, array $values = [], string $index = null, bool $assoc = true){
		pg_prepare($this->db, '', $this->replaceParams($sql));

		return $this->__getResult(pg_execute($this->db, '', $values), $index, $assoc);
	}

	/**
	 * @inheritdoc
	 */
	protected function multi_query(string $sql, array $values){
		pg_prepare($this->db, '', $this->replaceParams($sql));

		foreach($values as $row){
			pg_execute($this->db, '', $row);
		}

		return true;
	}

	/**
	 * @inheritdoc
	 */
	protected function multi_callback_query(string $sql, array $data, $callback){
		pg_prepare($this->db, '', $this->replaceParams($sql));

		foreach($data as $k => $row){
			pg_execute($this->db, '', call_user_func_array($callback, [$row, $k]));
		}

		return true;
	}

	/**
	 * @inheritdoc
	 */
	protected function replaceParams(string $sql):string{
		$i = 0;

		return preg_replace_callback('/(\?)/', function() use (&$i){
			return '$'.++$i;
		}, $sql);
	}

}
