<?php
/**
 * Class SQLitePDO
 *
 * @filesource   SQLiteDriver.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Dialects\SQLite;
use chillerlan\Database\Result;
use Exception, PDO;

/**
 * @property \PDO $db
 */
class SQLitePDO extends PDODriverAbstract{

	protected $drivername = 'sqlite';
	protected $dialect    = SQLite::class;

	/**
	 * @inheritdoc
	 *
	 * @link http://php.net/manual/ref.pdo-sqlite.connection.php
	 */
	protected function getDSN():string {
		return $this->drivername.':'.$this->options->database;
	}

	/** @inheritdoc */
	public function getServerInfo():?string {
		return $this->drivername.', connected to: '.$this->options->database.' (PDO::ATTR_SERVER_INFO not available)';
	}

	/** @inheritdoc */
	public function connect():DriverInterface{

		if($this->db instanceof PDO){
			return $this;
		}

		$db = $this->options->database;

		try{

			if($db !== ':memory:' && !is_file($db)){
				trigger_error('file not found');
			}

			if($db === ':memory:'){
				$this->pdo_options += [PDO::ATTR_PERSISTENT => true];
			}

			$this->db = new PDO($this->getDSN(), null, null, $this->pdo_options);

			return $this;
		}
		catch(Exception $e){
			throw new DriverException('db error: [SQLitePDO]: '.$e->getMessage());
		}
	}

/*
	protected function getResult(callable $callable, array $args, string $index = null, bool $assoc = null){
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
