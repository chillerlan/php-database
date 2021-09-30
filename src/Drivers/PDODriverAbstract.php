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

namespace chillerlan\Database\Drivers;

use PDO, PDOStatement, Throwable;

use function call_user_func_array, explode, get_called_class, gettype, is_bool, trim;

/**
 *
 */
abstract class PDODriverAbstract extends DriverAbstract{

	/**
	 * Holds the database resource object
	 */
	protected ?PDO $db = null;

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

	/**
	 * @inheritdoc
	 */
	public function connect():DriverInterface{

		if($this->db instanceof PDO){
			return $this;
		}

		try{
			$this->db = new PDO($this->getDSN(), $this->options->username, $this->options->password, $this->pdo_options);

			return $this;
		}
		catch(Throwable $e){
			throw new DriverException('db error: [PDODriver '.get_called_class().']: '.$e->getMessage());
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
	public function getDBResource():?PDO{
		return $this->db;
	}

	/**
	 * @inheritdoc
	 */
	public function getClientInfo():string{

		if(!$this->db instanceof PDO){
			return 'disconnected, no info available';
		}

		return $this->db->getAttribute(PDO::ATTR_CLIENT_VERSION);
	}

	/**
	 * @inheritdoc
	 */
	public function getServerInfo():string{

		if(!$this->db instanceof PDO){
			return 'disconnected, no info available';
		}

		return (string)$this->db->getAttribute(PDO::ATTR_SERVER_INFO);
	}

	/**
	 * @return bool|\chillerlan\Database\Result
	 */
	protected function get_result($stmt, string $index = null, bool $assoc = null){
		$assoc = $assoc ?? true;

		if(is_bool($stmt)){
			return $stmt;
		}

		return parent::getResult([$stmt, 'fetch'], [$assoc ? PDO::FETCH_ASSOC : PDO::FETCH_NUM], $index, $assoc);
	}

	/**
	 * @inheritdoc
	 */
	protected function raw_query(string $sql, string $index = null, bool $assoc = null){
		return $this->get_result($this->db->query($sql), $index, $assoc);
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

		return $this->get_result($stmt, $index, $assoc);
	}

	/**
	 * @inheritdoc
	 */
	protected function multi_query(string $sql, array $values):bool{
		$stmt = $this->db->prepare($sql, $this->pdo_stmt_options);

		foreach($values as $row){
			$this->bindParams($stmt, $row);
			$stmt->execute();
		}

		unset($stmt);

		return true;
	}

	/**
	 * @inheritdoc
	 */
	protected function multi_callback_query(string $sql, array $data, $callback):bool{
		$stmt = $this->db->prepare($sql, $this->pdo_stmt_options);

		foreach($data as $k => $row){
			$this->bindParams($stmt, call_user_func_array($callback, [$row, $k]));
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

	/**
	 *
	 */
	protected function bindParams(PDOStatement $stmt, array $values):void{
		$param_no = 1;

		foreach($values as $v){
			$t    = gettype($v);
			$type = PDO::PARAM_STR;

			if($t === 'boolean'){
				$type = PDO::PARAM_BOOL;
			}
			elseif($t === 'integer'){
				$type = PDO::PARAM_INT;
			}
			elseif($t === 'NULL'){
				$type = PDO::PARAM_NULL;
			}

			$stmt->bindValue($param_no, $v, $type);
			$param_no++;
		}
	}

}
