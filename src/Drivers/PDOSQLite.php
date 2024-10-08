<?php
/**
 * Class PDOSQLite
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
 */
declare(strict_types=1);

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Dialects\SQLite;
use Throwable, PDO;
use function is_file, is_readable, is_writable;

/**
 *
 */
final class PDOSQLite extends PDODriverAbstract{

	protected const DIALECT = SQLite::class;

	public function connect():DriverInterface{

		if($this->db instanceof PDO){
			return $this;
		}

		$db = $this->options->database;

		if($db !== ':memory:' && (!is_file($db) || !is_readable($db) || !is_writable($db))){
			throw new DriverException('database file not found');
		}

		try{

			if($db === ':memory:'){
				$this->pdo_options += [PDO::ATTR_PERSISTENT => true];
			}

			$this->db = new PDO($this->getDSN(), null, null, $this->pdo_options);

			return $this;
		}
		catch(Throwable $e){
			throw new DriverException('db error: [PDOSQLite]: '.$e->getMessage());
		}
	}

	public function getServerInfo():string{

		if(!$this->db instanceof PDO){
			return 'disconnected, no info available';
		}

		return static::class.', connected to: '.$this->options->database;
	}

	/**
	 * @see https://www.php.net/manual/ref.pdo-sqlite.connection.php
	 *
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	protected function getDSN():string{

		if(empty($this->options->database)){
			throw new DriverException('no database given');
		}

		return 'sqlite:'.$this->options->database;
	}

/*
	private function get_result(callable $callable, array $args, string $index = null, bool $assoc = null){
		$out = new Result(null, $this->convert_encoding_src, $this->convert_encoding_dest);
		$i   = 0;

		while($row = call_user_func_array($callable, $args)){
			$key = $assoc && !empty($index) ? $row[$index] : $i;

			foreach($row as $k => $v){
				switch(true){
					case is_numeric($v): $row[$k] = $v + 0; break;
				}
			}

			$out[$key] = $row;
			$i++;
		}

		return $i === 0 ? true : $out;
	}
*/

}
