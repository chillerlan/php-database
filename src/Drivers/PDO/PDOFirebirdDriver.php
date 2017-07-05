<?php
/**
 * Class PDOFirebirdDriver
 *
 * @filesource   PDOFirebirdDriver.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers\PDO
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers\PDO;

use chillerlan\Database\Drivers\{DriverException, DriverInterface};
use Exception, PDO;

/**
 * @todo: STRTOLOWER UNQUOTED COLUMN NAMES
 */
class PDOFirebirdDriver extends PDODriverAbstract{

	/**
	 * @inheritdoc
	 */
	protected $drivername = 'firebird';

	/**
	 * @inheritdoc
	 *
	 * @link http://php.net/manual/ref.pdo-firebird.connection.php
	 */
	protected function getDSN():string {
		$dsn = $this->drivername.':dbname=';

		if($this->options->host){
			$dsn .= $this->options->host;

			if(is_numeric($this->options->port)){
				$dsn .= '/'.$this->options->port;
			}

			$dsn .= ':';
		}

		$dsn .= $this->options->database;

		return $dsn;
	}

	/**
	 * @inheritdoc
	 */
	public function connect():DriverInterface{

		if($this->db instanceof PDO){
			return $this;
		}

		try{

			if(!is_file($this->options->database)){
				trigger_error('file not found');
			}

			$this->db = new PDO($this->getDSN(), $this->options->username, $this->options->password, $this->pdo_options);

			return $this;
		}
		catch(Exception $e){
			throw new DriverException('db error: [PDOFirebirdDriver]: '.$e->getMessage()); // @codeCoverageIgnore
		}

	}

	/**
	 * @inheritdoc
	 *
	 * @codeCoverageIgnore Firebird -> SQLSTATE[IM001]: driver does not support lastInsertId()
	 */
	protected function insertID():string {
		return '';
	}

	/**
	 * @inheritdoc
	 */
	public function getServerInfo():string{
		return $this->db->getAttribute(PDO::ATTR_SERVER_INFO).', connected to: '.$this->options->database;
	}

}
