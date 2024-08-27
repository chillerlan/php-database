<?php
/**
 * Class PDOMySQL
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

use chillerlan\Database\Dialects\MySQL;
use PDO;
use function is_numeric;

/**
 *
 */
final class PDOMySQL extends PDODriverAbstract{

	protected const DIALECT = MySQL::class;

	/**
	 * @see https://www.php.net/manual/ref.pdo-mysql.connection.php
	 *
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	protected function getDSN():string{

		if(empty($this->options->database)){
			throw new DriverException('no database given');
		}

		$dsn = 'mysql';

		if($this->options->socket){
			$dsn .= ':unix_socket='.$this->options->socket;
		}
		else{

			if(empty($this->options->host)){
				throw new DriverException('no host given');
			}

			$dsn .= ':host='.$this->options->host;

			if(is_numeric($this->options->port)){
				$dsn .= ';port='.$this->options->port;
			}

		}

		$dsn .= ';dbname='.$this->options->database;

		// the charset option is specific to mysql
		if($this->options->mysql_charset){
			$dsn .= ';charset='.$this->options->mysql_charset;
		}

		return $dsn;
	}

	public function connect():DriverInterface{

		if($this->options->use_ssl){
			$this->pdo_options += [
				PDO::MYSQL_ATTR_SSL_KEY    => $this->options->ssl_key,
				PDO::MYSQL_ATTR_SSL_CERT   => $this->options->ssl_cert,
				PDO::MYSQL_ATTR_SSL_CA     => $this->options->ssl_ca,
				PDO::MYSQL_ATTR_SSL_CAPATH => $this->options->ssl_capath,
				PDO::MYSQL_ATTR_SSL_CIPHER => $this->options->ssl_cipher,
			];
		}

		return parent::connect();
	}

}
