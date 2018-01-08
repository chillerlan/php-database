<?php
/**
 * Class PDODriverAbstract
 *
 * @filesource   PDODriverAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers\PDO
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers;

use PDO;
use PDOStatement;

/**
 * @property \PDO $db
 */
abstract class PDODriverAbstract extends DriverAbstract{

	/**
	 * The PDO drivername which is being used in the DSN
	 *
	 * @var string
	 */
	protected $drivername;

	/**
	 * Some basic PDO options
	 *
	 * @link http://php.net/manual/pdo.getattribute.php
	 * @link http://php.net/manual/pdo.constants.php
	 *
	 * @var array
	 */
	protected $pdo_options = [
		PDO::ATTR_CASE              => PDO::CASE_NATURAL,
		PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_STRINGIFY_FETCHES => false,
		PDO::ATTR_EMULATE_PREPARES  => false, // will break mssql
	];

	/**
	 * @var array
	 */
	protected $pdo_stmt_options = [];

	/**
	 * Returns a DSN string using the given options
	 *
	 * @return string DSN
	 */
	protected function getDSN():string{
		$dsn = $this->drivername;

		if($this->options->socket){
			$dsn .= ':unix_socket='.$this->options->socket; // @codeCoverageIgnore
		}
		else{
			$dsn .= ':host='.$this->options->host;
			$dsn .= is_numeric($this->options->port) ? ';port='.$this->options->port : '';
		}

		$dsn .= ';dbname='.$this->options->database;

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
			// @codeCoverageIgnoreStart
			if($this->options->use_ssl){
				$this->pdo_options += [
					PDO::MYSQL_ATTR_SSL_KEY    => $this->options->ssl_key,
					PDO::MYSQL_ATTR_SSL_CERT   => $this->options->ssl_cert,
					PDO::MYSQL_ATTR_SSL_CA     => $this->options->ssl_ca,
					PDO::MYSQL_ATTR_SSL_CAPATH => $this->options->ssl_capath,
					PDO::MYSQL_ATTR_SSL_CIPHER => $this->options->ssl_cipher,
				];
			}
			// @codeCoverageIgnoreEnd

			$this->db = new PDO($this->getDSN(), $this->options->username, $this->options->password, $this->pdo_options);

			return $this;
		}
		catch(\Exception $e){

			// PDOMSSQL workaround
			// @codeCoverageIgnoreStart
			if(trim(explode(':', $e->getMessage(), 2)[0]) === 'SQLSTATE[IMSSP]'){
				return $this;
			}
			// @codeCoverageIgnoreEnd

			throw new DriverException('db error: [PDODriver '.$this->drivername.']: '.$e->getMessage());
		}
	}

	/**
	 * @inheritdoc
	 */
	public function disconnect():bool{
		$this->db = null;

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function getClientInfo():string{
		return $this->db->getAttribute(PDO::ATTR_CLIENT_VERSION);
	}

	/**
	 * @inheritdoc
	 */
	public function getServerInfo():string{
		return $this->db->getAttribute(PDO::ATTR_SERVER_INFO);
	}

	/**
	 * @inheritdoc
	 */
	public function escape($data):string {
		return $this->db->quote($data);
	}

	/**
	 * Returns the last insert id (if present)
	 *
	 * @link http://php.net/manual/pdo.lastinsertid.php
	 * @return string
	 */
	protected function insertID():string{
		return $this->db->lastInsertId();
	}

	/**
	 * @param \PDOStatement $stmt
	 * @param array         $values
	 *
	 * @return void
	 */
	protected function bindParams(PDOStatement &$stmt, array $values){
		$param_no = 1;

		foreach($values as $v){

			switch(gettype($v)){
				case 'boolean': $type = PDO::PARAM_BOOL; break;
				case 'integer': $type = PDO::PARAM_INT;  break;
				case 'NULL'   : $type = PDO::PARAM_NULL; break;
				default:        $type = PDO::PARAM_STR;  break;
			}

			$stmt->bindValue($param_no, $v, $type);
			$param_no++;
		}
	}

	/**
	 * @param             $stmt
	 * @param string|null $index
	 * @param bool        $assoc
	 *
	 * @return bool|\chillerlan\Database\Result
	 */
	protected function __getResult($stmt, string $index = null, bool $assoc = null){
		$assoc = $assoc !== null ? $assoc : true;

		if(is_bool($stmt)){
			return $stmt; // @codeCoverageIgnore
		}

		return $this->getResult([$stmt, 'fetch'], [$assoc ? PDO::FETCH_ASSOC : PDO::FETCH_NUM], $index, $assoc);
	}

	/**
	 * @inheritdoc
	 */
	protected function raw_query(string $sql, string $index = null, bool $assoc = null){
		return $this->__getResult($this->db->query($sql), $index, $assoc);
	}

	/**
	 * @inheritdoc
	 */
	protected function prepared_query(string $sql, array $values = null, string $index = null, bool $assoc = null){
		$stmt = $this->db->prepare($sql, $this->pdo_stmt_options);

		if(!empty($values)){
			$this->bindParams($stmt, $values);
		}

		$stmt->execute();

		return $this->__getResult($stmt, $index, $assoc);
	}

	/**
	 * @inheritdoc
	 */
	protected function multi_query(string $sql, array $values){
		$stmt = $this->db->prepare($sql, $this->pdo_stmt_options);

		foreach($values as $row){
			$this->bindParams($stmt, $row);
			$stmt->execute();
		}

		$stmt = null;

		return true;
	}

	/**
	 * @inheritdoc
	 */
	protected function multi_callback_query(string $sql, array $data, $callback){
		$stmt = $this->db->prepare($sql, $this->pdo_stmt_options);

		foreach($data as $k => $row){
			$this->bindParams($stmt, call_user_func_array($callback, [$row, $k]));
			$stmt->execute();
		}

		$stmt = null;

		return true;
	}

}
