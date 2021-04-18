<?php
/**
 * Class FirebirdPDO
 *
 * @filesource   FirebirdPDO.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Dialects\Firebird;
use Exception, PDO;

/**
 * @property \PDO $db
 */
class FirebirdPDO extends PDODriverAbstract{

	protected string $drivername = 'firebird';
	protected string $dialect    = Firebird::class;

	/**
	 * @inheritdoc
	 *
	 * @link http://php.net/manual/ref.pdo-firebird.connection.php
	 */
	protected function getDSN():string {
		$dsn = $this->drivername.':dbname=';

		if($this->options->host !== null){
			$dsn .= $this->options->host;

			if(is_numeric($this->options->port)){
				$dsn .= '/'.$this->options->port;
			}

			$dsn .= ':';
		}

		$dsn .= $this->options->database;

		if($this->options->firebird_encoding !== null){
			$dsn .= ';encoding='.$this->options->firebird_encoding;
		}

		return $dsn;
	}

	/** @inheritdoc */
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
			throw new DriverException('db error: [FirebirdPDO]: '.$e->getMessage()); // @codeCoverageIgnore
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

	/** @inheritdoc */
	public function getServerInfo():?string{
		return $this->db->getAttribute(PDO::ATTR_SERVER_INFO).', connected to: '.$this->options->database;
	}

}
