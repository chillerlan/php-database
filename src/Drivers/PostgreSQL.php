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
use PgSql\Connection as PgSqlConnection;
use PgSql\Result as PgSqlResult;
use Closure, Throwable;
use function implode, in_array, pg_close, pg_connect, pg_execute, pg_fetch_assoc, pg_fetch_row, pg_field_type,
	pg_free_result, pg_last_error, pg_prepare, pg_query, pg_version, preg_replace_callback, sodium_bin2hex;

/**
 *
 */
final class PostgreSQL extends DriverAbstract{

	/**
	 * Holds the database resource object
	 */
	private PgSqlConnection|null $db = null;

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

	public function getDBResource():PgSqlConnection|null{
		return $this->db;
	}

	public function getDialect():Dialect{
		return new Postgres;
	}

	public function getClientInfo():string{

		if($this->db === null){
			return 'disconnected, no info available';
		}

		$ver = pg_version($this->db);

		return 'PostgreSQL '.$ver['client'].' ('.$ver['client_encoding'].')';
	}

	public function getServerInfo():string{

		if($this->db === null){
			return 'disconnected, no info available';
		}

		$ver = pg_version($this->db);

		return 'PostgreSQL '.$ver['server'].' ('.$ver['server_encoding'].', date style: '
		       .$ver['DateStyle'].', time zone: '.$ver['TimeZone'].')';
	}

	/**
	 * @see https://stackoverflow.com/a/44814220
	 */
	protected function escape_string(string $string):string{

		if($string === ''){
			return "''";
		}

		// convert to hex literal, emulating mysql's UNHEX() (seriously pgsql??)
		return "encode(decode('".sodium_bin2hex($string)."', 'hex'), 'escape')";
	}

	protected function raw_query(string $sql, string|null $index = null, bool|null $assoc = null):Result{
		return $this->get_result(pg_query($this->db, $sql), $index, $assoc);
	}

	protected function prepared_query(string $sql, array|null $values = null, string|null $index = null, bool|null $assoc = null):Result{
		pg_prepare($this->db, '', $this->replaceParams($sql));

		return $this->get_result(pg_execute($this->db, '', $values ?? []), $index, $assoc);
	}

	protected function multi_query(string $sql, array $values):bool{
		$p = pg_prepare($this->db, '', $this->replaceParams($sql));

		if($p === false){
			throw new DriverException(pg_last_error($this->db));
		}

		foreach($values as $row){
			pg_execute($this->db, '', $row);
		}

		return true;
	}

	protected function multi_callback_query(string $sql, array $data, Closure $callback):bool{
		$p = pg_prepare($this->db, '', $this->replaceParams($sql));

		if($p === false){
			throw new DriverException(pg_last_error($this->db));
		}

		foreach($data as $k => $row){
			pg_execute($this->db, '', $callback($row, $k));
		}

		return true;
	}

	private function get_result(PgSqlResult|false $result, string|null $index = null, bool|null $assoc = null):Result{

		if($result === false){
			throw new DriverException(pg_last_error($this->db));
		}

		$out = new Result(null, $this->convert_encoding_src, $this->convert_encoding_dest);
		$i   = 0;

		/** @noinspection PhpAssignmentInConditionInspection */
		while($row = ($assoc ? pg_fetch_assoc($result) : pg_fetch_row($result))){
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

	private function replaceParams(string $sql):string{
		$i = 1;

		/** @phan-suppress-next-line PhanTypeMismatchArgumentInternal */
		return preg_replace_callback(pattern: '/(\?)/', callback: function() use (&$i){
			return '$'.$i++;
		}, subject: $sql);
	}

}
