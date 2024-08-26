<?php
/**
 * Class PDOMSSqlSrv
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Result;
use chillerlan\Database\Dialects\{Dialect, MSSQL};
use chillerlan\Settings\SettingsContainerInterface;
use Psr\Log\{LoggerInterface, NullLogger};
use Psr\SimpleCache\CacheInterface;
use PDO, Throwable;
use function explode, is_array, is_numeric, sodium_bin2hex, strpos, strtolower, trim;
use const PHP_OS;

/**
 *
 */
final class PDOMSSqlSrv extends PDODriverAbstract{

	/**
	 * PDOMSSqlSrv constructor.
	 *
	 * @phan-suppress PhanUndeclaredConstantOfClass
	 */
	public function __construct(SettingsContainerInterface $options, CacheInterface|null $cache = null, LoggerInterface $logger = new NullLogger){
		// setting this with any value breaks
		unset($this->pdo_options[PDO::ATTR_EMULATE_PREPARES]);

		$this->pdo_options[PDO::SQLSRV_ATTR_QUERY_TIMEOUT]        = $options->mssql_timeout; // doesn't seem to have an effect
		$this->pdo_options[PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE] = true;
		$this->pdo_options[PDO::SQLSRV_ATTR_ENCODING]             = PDO::SQLSRV_ENCODING_UTF8;

		parent::__construct($options, $cache, $logger);
	}

	public function getDialect():Dialect{
		return new MSSQL;
	}

	/**
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	public function getClientInfo():string{

		if(!$this->db instanceof PDO){
			return 'disconnected, no info available';
		}

		$info = $this->db->getAttribute(PDO::ATTR_CLIENT_VERSION);

		if(is_array($info) && !empty($info)){
			return $info['DriverVer'].' - '.$info['ExtensionVer'].' - '.$info['DriverODBCVer'];
		}

		throw new DriverException('error retrieving client info'); // @codeCoverageIgnore
	}

	/**
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	public function getServerInfo():string{

		if(!$this->db instanceof PDO){
			return 'disconnected, no info available';
		}

		$info = $this->db->getAttribute(PDO::ATTR_SERVER_INFO);

		if(is_array($info) && !empty($info)){
			return PHP_OS.' - '.$info['SQLServerVersion'].' - '.$info['SQLServerName'].'/'.$info['CurrentDatabase'];
		}

		throw new DriverException('error retrieving server info'); // @codeCoverageIgnore
	}

	/**
	 * @see https://www.php.net/manual/ref.pdo-sqlsrv.connection.php
	 *
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	protected function getDSN():string{

		/*
		//use any of these or check exact MSSQL ODBC drivername in "ODBC Data Source Administrator"
		$mssqldriver = '{SQL Server}';
		$mssqldriver = '{SQL Server Native Client 11.0}';
		$mssqldriver = '{ODBC Driver 11 for SQL Server}';

		$hostname='127.0.0.1';
		$dbname='test';
		$username='user';
		$password='pw';
		$dbDB = new PDO("odbc:Driver=$mssqldriver;Server=$hostname;Database=$dbname", $username, $password);
		*/

		if(empty($this->options->host)){
			throw new DriverException('no host given');
		}

		if(empty($this->options->database)){
			throw new DriverException('no database given');
		}

		$dsn = 'sqlsrv:Server='.$this->options->host;

		if(is_numeric($this->options->port)){
			$dsn .= ','.$this->options->port;
		}

		$dsn .= ';Database='.$this->options->database;

		return $dsn;
	}

	public function connect():DriverInterface{

		if($this->db instanceof PDO){
			return $this;
		}

		try{
			$this->db = new PDO($this->getDSN(), $this->options->username, $this->options->password, $this->pdo_options);

			return $this;
		}
		catch(Throwable $e){

			// PDOMSSQL workaround
			if(trim(explode(':', $e->getMessage(), 2)[0]) === 'SQLSTATE[IMSSP]'){
				return $this;
			}

			throw new DriverException('db error: [PDOMSSqlSrv]: '.$e->getMessage());
		}
	}


	/**
	 * @see https://docs.microsoft.com/sql/t-sql/data-types/constants-transact-sql?view=sql-server-ver15
	 */
	protected function escape_string(string $string):string{

		if($string === ''){
			return "''";
		}

		// convert to hex literal, sql server only accepts the 0x... format
		return '0x'.sodium_bin2hex($string);
	}

	protected function raw_query(string $sql, string|null $index = null, bool|null $assoc = null):Result{

		try{
			return parent::raw_query($sql, $index, $assoc);
		}
		catch(Throwable $e){
			return new Result(null, null, null, true, $this->silenceNonErrorException($e));
		}

	}

	/**
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	private function silenceNonErrorException(Throwable $e):bool{
		$message = $e->getMessage();

		// @todo silence.
		$x = explode(':', $message, 2);

		if(trim($x[0]) === 'SQLSTATE[IMSSP]'){

			if(strpos(strtolower($x[1]), 'no fields') > 0){
				return true;
			}

		}

		throw new DriverException('sql error: ['.static::class.'::raw()]'.$message);
	}

	protected function prepared_query(string $sql, array|null $values = null, string|null $index = null, bool|null $assoc = null):Result{

		try{
			return parent::prepared_query($sql, $values, $index, $assoc);
		}
		catch(Throwable $e){
			return new Result(null, null, null, true, $this->silenceNonErrorException($e));
		}

	}

}
