<?php
/**
 * Class MSSqlSrvPDO
 *
 * @filesource   MSSqlSrvPDO.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Dialects\MSSQL;
use chillerlan\Settings\SettingsContainerInterface;
use PDO;
use Psr\{
	Log\LoggerInterface, SimpleCache\CacheInterface
};

/**
 * @property \PDO $db
 */
class MSSqlSrvPDO extends PDODriverAbstract{

	protected string $drivername = 'sqlsrv';
	protected string $dialect    = MSSQL::class;

	/**
	 * MSSqlSrvPDO constructor.
	 *
	 * @param \chillerlan\Settings\SettingsContainerInterface $options
	 * @param \Psr\SimpleCache\CacheInterface|null $cache
	 * @param \Psr\Log\LoggerInterface|null        $log
	 */
	public function __construct(SettingsContainerInterface $options, CacheInterface $cache = null, LoggerInterface $log = null){
		unset($this->pdo_options[PDO::ATTR_EMULATE_PREPARES]);

		// @todo
		$this->pdo_options[PDO::SQLSRV_ATTR_QUERY_TIMEOUT] = $options->mssql_timeout; // doesn't seem to have an effect
		$this->pdo_options[PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE] = true;
		$this->pdo_options[PDO::SQLSRV_ATTR_ENCODING ] = PDO::SQLSRV_ENCODING_UTF8;

		parent::__construct($options, $cache);
	}

	/** @inheritdoc */
	protected function getDSN():string{
		$dsn = $this->drivername;

		$dsn .= ':Server='.$this->options->host;

		if(is_numeric($this->options->port)){
			$dsn .=  ','.$this->options->port;
		}

		$dsn .= ';Database='.$this->options->database;

		return $dsn;
	}

	/** @inheritdoc */
	public function getClientInfo():string{
		$info = $this->db->getAttribute(PDO::ATTR_CLIENT_VERSION);

		if(is_array($info) && !empty($info)){
			return $info['DriverVer'].' - '.$info['ExtensionVer'].' - '.$info['DriverODBCVer'];
		}

		return 'disconnected, no info available';
	}

	/** @inheritdoc */
	public function getServerInfo():?string{
		$info = $this->db->getAttribute(PDO::ATTR_SERVER_INFO);

		if(is_array($info) && !empty($info)){
			return PHP_OS.' - '.$info['SQLServerVersion'].' - '.$info['SQLServerName'].'/'.$info['CurrentDatabase'];
		}

		return 'disconnected, no info available';
	}

	/** @inheritdoc */
	public function raw(string $sql, string $index = null, bool $assoc = null){

		try{
			return $this->raw_query($sql, $index, $assoc);
		}
		catch(\Exception $e){
			return $this->silenceNonErrorException($e);
		}

	}

	/** @inheritdoc */
	public function prepared(string $sql, array $values = null, string $index = null, bool $assoc = null){

		try{
			return $this->prepared_query($sql, $values, $index, $assoc);
		}
		catch(\Exception $e){
			return $this->silenceNonErrorException($e);
		}

	}

	/**
	 * @param \Exception $e
	 *
	 * @return bool
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	protected function silenceNonErrorException(\Exception $e){
		$message = $e->getMessage();

		// @todo silence.
		$x = explode(':', $message, 2);

		if(trim($x[0]) === 'SQLSTATE[IMSSP]'){

			if(strpos(strtolower($x[1]), 'no fields') > 0){
				return true;
			}

		}

		throw new DriverException('sql error: ['.get_called_class().'::raw()]'.$message);
	}

}
