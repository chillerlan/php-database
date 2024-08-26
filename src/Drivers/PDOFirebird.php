<?php
/**
 * Class PDOFirebird
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Dialects\{Dialect, Firebird};
use Exception, Throwable, PDO;
use function is_file, is_numeric, is_readable, is_writable;

/**
 *
 */
final class PDOFirebird extends PDODriverAbstract{

	public function getDialect():Dialect{
		return new Firebird;
	}

	/**
	 * @see https://www.php.net/manual/ref.pdo-firebird.connection.php
	 *
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	protected function getDSN():string{

		if(empty($this->options->database)){
			// technically covered by is_file() in $this::connect()
			throw new DriverException('no database given');
		}

		$dsn = 'firebird:dbname=';

		if($this->options->host){
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

	public function connect():DriverInterface{

		if($this->db instanceof PDO){
			return $this;
		}

		$db = $this->options->database;

		try{

			if(!is_file($db) || !is_readable($db) || !is_writable($db)){
				throw new Exception('invalid database file');
			}

			$this->db = new PDO($this->getDSN(), $this->options->username, $this->options->password, $this->pdo_options);

			return $this;
		}
		catch(Throwable $e){
			throw new DriverException('db error: [PDOFirebird]: '.$e->getMessage());
		}

	}

	/**
	 * @codeCoverageIgnore Firebird -> SQLSTATE[IM001]: driver does not support lastInsertId()
	 */
	protected function insertID():string{
		return '';
	}

	public function getServerInfo():string{

		if(!$this->db instanceof PDO){
			return 'disconnected, no info available';
		}

		return $this->db->getAttribute(PDO::ATTR_SERVER_INFO).', connected to: '.$this->options->database;
	}

}
