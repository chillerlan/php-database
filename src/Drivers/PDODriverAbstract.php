<?php
/**
 * Class PDODriverAbstract
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

use chillerlan\Database\Result;
use Closure, PDO, PDOStatement, Throwable;
use function gettype, is_bool;

/**
 *
 */
abstract class PDODriverAbstract extends DriverAbstract{

	/**
	 * Holds the database resource object
	 */
	protected PDO|null $db = null;

	/**
	 * Some basic PDO options
	 *
	 * @link http://php.net/manual/pdo.getattribute.php
	 * @link http://php.net/manual/pdo.constants.php
	 */
	protected array $pdo_options = [
		PDO::ATTR_CASE              => PDO::CASE_NATURAL,
		PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_STRINGIFY_FETCHES => false,
		PDO::ATTR_EMULATE_PREPARES  => false, // will break mssql
	];

	protected array $pdo_stmt_options = [];

	/**
	 * Returns a DSN string using the given options
	 */
	abstract protected function getDSN():string;

	public function connect():DriverInterface{

		if($this->db instanceof PDO){
			return $this;
		}

		try{
			$this->db = new PDO($this->getDSN(), $this->options->username, $this->options->password, $this->pdo_options);

			return $this;
		}
		catch(Throwable $e){
			throw new DriverException('db error: [PDODriver '.static::class.']: '.$e->getMessage());
		}

	}

	public function disconnect():bool{
		$this->db = null;

		return true;
	}

	public function getDBResource():PDO|null{
		return $this->db;
	}

	public function getClientInfo():string{

		if(!$this->db instanceof PDO){
			return 'disconnected, no info available';
		}

		return $this->db->getAttribute(PDO::ATTR_CLIENT_VERSION);
	}

	public function getServerInfo():string{

		if(!$this->db instanceof PDO){
			return 'disconnected, no info available';
		}

		return (string)$this->db->getAttribute(PDO::ATTR_SERVER_INFO);
	}

	protected function get_result($stmt, string|null $index = null, bool|null $assoc = null):Result{
		$assoc = $assoc ?? true;

		if(is_bool($stmt)){
			return new Result(null, null, null, true, $stmt);
		}

		return parent::getResult($stmt->fetch(...), [$assoc ? PDO::FETCH_ASSOC : PDO::FETCH_NUM], $index, $assoc);
	}

	protected function raw_query(string $sql, string|null $index = null, bool|null $assoc = null):Result{
		return $this->get_result($this->db->query($sql), $index, $assoc);
	}

	protected function prepared_query(string $sql, array|null $values = null, string|null $index = null, bool|null $assoc = null):Result{
		$stmt = $this->db->prepare($sql, $this->pdo_stmt_options);

		if(!empty($values)){
			$this->bindParams($stmt, $values);
		}

		$stmt->execute();

		return $this->get_result($stmt, $index, $assoc);
	}

	protected function multi_query(string $sql, array $values):bool{
		$stmt = $this->db->prepare($sql, $this->pdo_stmt_options);

		foreach($values as $row){
			$this->bindParams($stmt, $row);
			$stmt->execute();
		}

		unset($stmt);

		return true;
	}

	protected function multi_callback_query(string $sql, array $data, Closure $callback):bool{
		$stmt = $this->db->prepare($sql, $this->pdo_stmt_options);

		foreach($data as $k => $row){
			$this->bindParams($stmt, $callback($row, $k));
			$stmt->execute();
		}

		unset($stmt);

		return true;
	}

	/**
	 * Returns the last insert id (if present)
	 * @link http://php.net/manual/pdo.lastinsertid.php
	 */
	protected function insertID():string{
		return $this->db->lastInsertId();
	}

	protected function bindParams(PDOStatement $stmt, array $values):void{
		$param_no = 1;

		foreach($values as $v){
			// https://wiki.php.net/rfc/pdo_float_type ???
			$type = match(gettype($v)){
				'boolean' => PDO::PARAM_BOOL,
				'integer' => PDO::PARAM_INT,
				'NULL'    => PDO::PARAM_NULL,
				default   => PDO::PARAM_STR,
			};

			$stmt->bindValue($param_no, $v, $type);
			$param_no++;
		}
	}

}
