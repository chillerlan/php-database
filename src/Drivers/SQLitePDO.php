<?php
/**
 * Class SQLitePDO
 *
 * @filesource   SQLiteDriver.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers\PDO
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Query\SQLiteQuery;
use Exception;
use PDO;

/**
 * @property \PDO $db
 */
class SQLitePDO extends PDODriverAbstract{

	protected $drivername   = 'sqlite';
	protected $querybuilder = SQLiteQuery::class;

	/**
	 * @inheritdoc
	 *
	 * @link http://php.net/manual/ref.pdo-sqlite.connection.php
	 */
	protected function getDSN():string {
		return $this->drivername.':'.$this->options->database;
	}

	/** @inheritdoc */
	public function getServerInfo():string {
		return $this->drivername.', connected to: '.$this->options->database.' (PDO::ATTR_SERVER_INFO not available)';
	}

	/** @inheritdoc */
	public function connect():DriverInterface{

		if($this->db instanceof PDO){
			return $this;
		}

		try{

			if($this->options->database !== ':memory:' && !is_file($this->options->database)){
				trigger_error('file not found');
			}

			if($this->options->database === ':memory:'){
				$this->pdo_options += [PDO::ATTR_PERSISTENT => true];
			}

			$this->db = new PDO($this->getDSN(), null, null, $this->pdo_options);

			return $this;
		}
		catch(Exception $e){
			throw new DriverException('db error: [SQLitePDO]: '.$e->getMessage());
		}
	}


}
