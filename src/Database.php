<?php
/**
 * Class Database
 *
 * @filesource   Database.php
 * @created      27.06.2017
 * @package      chillerlan\Database
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database;

use chillerlan\Database\{
	Dialects\Dialect, Drivers\DriverInterface, Query\QueryBuilder
};

/**
 * @property \chillerlan\Database\Query\Alter    $alter
 * @property \chillerlan\Database\Query\Create   $create
 * @property \chillerlan\Database\Query\Delete   $delete
 * @property \chillerlan\Database\Query\Drop     $drop
 * @property \chillerlan\Database\Query\Insert   $insert
 * @property \chillerlan\Database\Query\Select   $select
 * @property \chillerlan\Database\Query\Show     $show
 * @property \chillerlan\Database\Query\Truncate $truncate
 * @property \chillerlan\Database\Query\Update   $update
 */
class Database extends DatabaseAbstract implements DriverInterface{

	/**
	 * @inheritdoc
	 * @codeCoverageIgnore
	 */
	public function __destruct(){
		$this->driver->disconnect();
	}

	/** @inheritdoc */
	public function __get(string $name){
		return $this->query->{$name};
	}

	/**
	 * @return \chillerlan\Database\Drivers\DriverInterface
	 */
	public function getDriver():DriverInterface{
		return $this->driver;
	}

	/**
	 * @return \chillerlan\Database\Query\QueryBuilder
	 */
	public function getQueryBuilder():QueryBuilder{
		return $this->query;
	}

	/**
	 * @inheritdoc
	 * @codeCoverageIgnore
	 */
	public function getDBResource(){
		return $this->driver->getDBResource();
	}

	/** @inheritdoc */
	public function connect():DriverInterface{
		$this->driver->connect();

		return $this;
	}

	/** @inheritdoc */
	public function disconnect():bool{
		return $this->driver->disconnect();
	}

	/** @inheritdoc */
	public function getClientInfo():string{
		return $this->driver->getClientInfo();
	}

	/** @inheritdoc */
	public function getServerInfo():string{
		return $this->driver->getServerInfo();
	}

	/** @inheritdoc */
	public function escape($data = null){
		return $this->driver->escape($data);
	}

	/** @inheritdoc */
	public function raw(string $sql, string $index = null, bool $assoc = null){
		return $this->driver->raw($sql, $index, $assoc);
	}

	/** @inheritdoc */
	public function rawCached(string $sql, string $index = null, bool $assoc = null, int $ttl = null){
		return $this->driver->rawCached($sql, $index, $assoc, $ttl);
	}

	/** @inheritdoc */
	public function prepared(string $sql, array $values = null, string $index = null, bool $assoc = null){
		return $this->driver->prepared($sql, $values, $index, $assoc);
	}

	/** @inheritdoc */
	public function preparedCached(string $sql, array $values = null, string $index = null, bool $assoc = null, int $ttl = null){
		return $this->driver->preparedCached($sql, $values, $index, $assoc, $ttl);
	}

	/** @inheritdoc */
	public function multi(string $sql, array $values):bool{
		return $this->driver->multi($sql, $values);
	}

	/** @inheritdoc */
	public function multiCallback(string $sql, array $data, $callback):bool{
		return $this->driver->multiCallback($sql, $data, $callback);
	}

	/** @inheritdoc */
	public function getDialect():Dialect{
		return $this->driver->getDialect();
	}

}
