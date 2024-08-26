<?php
/**
 * Class PDOPostgreSQL
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

use function is_numeric, sodium_bin2hex;

/**
 *
 */
final class PDOPostgreSQL extends PDODriverAbstract{

	public function getDialect():Dialect{
		return new Postgres;
	}

	/**
	 * Returns a DSN string using the given options
	 *
	 * @see https://www.php.net/manual/ref.pdo-pgsql.connection.php
	 *
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	protected function getDSN():string{

		if(empty($this->options->database)){
			throw new DriverException('no database given');
		}

		$dsn = 'pgsql';

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

		return $dsn;
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

}
