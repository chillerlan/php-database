<?php
/**
 * Class PostgreSQL
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Dialects\{Dialect, Postgres};
use chillerlan\Database\Result;
use Throwable;
use PgSql\Connection;

use function bin2hex, call_user_func_array, implode, in_array, is_bool, pg_close, pg_connect,
	pg_execute, pg_field_type, pg_free_result, pg_prepare, pg_query, pg_version, preg_replace_callback;

/**
 *
 */
final class PostgreSQL extends DriverAbstract{

	/**
	 * Holds the database resource object
	 *
	 * @var \PgSql\Connection|resource|null
	 */
	private ?Connection $db = null;

	/**
	 * @inheritdoc
	 */
	public function connect():DriverInterface{

		if($this->db !== null){
			return $this;
		}

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
		catch(Throwable $e){
			throw new DriverException('db error: [PostgreSQL]: '.$e->getMessage());
		}

	}

	/**
	 * @inheritdoc
	 */
	public function disconnect():bool{

		if($this->db !== null){
			$ret = pg_close($this->db);

			if($ret === true){
				$this->db = null;
			}

			return $ret;
		}

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function getDBResource():?Connection{
		return $this->db;
	}

	/**
	 * @inheritdoc
	 */
	public function getDialect():Dialect{
		return new Postgres;
	}

	/**
	 * @inheritdoc
	 */
	public function getClientInfo():string{

		if($this->db === null){
			return 'disconnected, no info available';
		}

		$ver = pg_version($this->db);

		return 'PostgreSQL '.$ver['client'].' ('.$ver['client_encoding'].')';
	}

	/**
	 * @inheritdoc
	 */
	public function getServerInfo():string{

		if($this->db === null){
			return 'disconnected, no info available';
		}

		$ver = pg_version($this->db);

		return 'PostgreSQL '.$ver['server'].' ('.$ver['server_encoding'].', date style: '
		       .$ver['DateStyle'].', time zone: '.$ver['TimeZone'].')';
	}

	/**
	 * @inheritdoc
	 *
	 * @see https://stackoverflow.com/a/44814220
	 */
	protected function escape_string(string $string):string{

		if($string === ''){
			return "''";
		}

		// convert to hex literal, emulating mysql's UNHEX() (seriously pgsql??)
		return "encode(decode('".bin2hex($string)."', 'hex'), 'escape')";
	}

	/**
	 * @inheritdoc
	 */
	protected function raw_query(string $sql, string $index = null, bool $assoc = null):Result{
		return $this->get_result(pg_query($this->db, $sql), $index, $assoc);
	}

	/**
	 * @inheritdoc
	 */
	protected function prepared_query(string $sql, array $values = null, string $index = null, bool $assoc = null):Result{
		pg_prepare($this->db, '', $this->replaceParams($sql));

		return $this->get_result(pg_execute($this->db, '', $values ?? []), $index, $assoc);
	}

	/**
	 * @inheritdoc
	 */
	protected function multi_query(string $sql, array $values):bool{
		pg_prepare($this->db, '', $this->replaceParams($sql));

		foreach($values as $row){
			pg_execute($this->db, '', $row);
		}

		return true;
	}

	/**
	 * @inheritdoc
	 */
	protected function multi_callback_query(string $sql, array $data, $callback):bool{
		pg_prepare($this->db, '', $this->replaceParams($sql));

		foreach($data as $k => $row){
			pg_execute($this->db, '', call_user_func_array($callback, [$row, $k]));
		}

		return true;
	}

	/**
	 * @inheritdoc
	 */
	private function get_result($result, string $index = null, bool $assoc = null):Result{

		if(is_bool($result)){
			return new Result(null, null, null, true, $result);
		}

		$out = new Result(null, $this->convert_encoding_src, $this->convert_encoding_dest);
		$i   = 0;

		/** @noinspection PhpAssignmentInConditionInspection */
		while($row = call_user_func_array($assoc ? 'pg_fetch_assoc' : 'pg_fetch_row', [$result])){
			$key = $i;

			$j = 0;
			foreach($row as &$value){
				// https://gitter.im/arenanet/api-cdi?at=594326ba31f589c64fafe554
				$fieldType = pg_field_type($result, $j);

				if($fieldType === 'bool'){
					$value = $value === 't';
				}
				elseif(in_array($fieldType, ['int2', 'int4', 'int8'], true)){
					$value = (int)$value;
				}
				elseif(in_array($fieldType, ['float4', 'float8'], true)){
					$value = (float)$value; // @codeCoverageIgnore
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

		return $out;
	}

	/**
	 *
	 */
	private function replaceParams(string $sql):string{
		$i = 1;

		/** @phan-suppress-next-line PhanTypeMismatchArgumentInternal - a Closure is callable... */
		return preg_replace_callback('/(\?)/', function() use (&$i){
			return '$'.$i++;
		}, $sql);
	}

}
