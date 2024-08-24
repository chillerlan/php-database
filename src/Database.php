<?php
/**
 * Class Database
 *
 * @created      27.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database;

use chillerlan\Database\Dialects\Dialect;
use chillerlan\Database\Drivers\DriverInterface;

class Database extends DatabaseAbstract implements DriverInterface{

	/**
	 * @inheritdoc
	 * @codeCoverageIgnore
	 */
	public function getDBResource():mixed{
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
	public function escape(mixed $data = null):mixed{
		return $this->driver->escape($data);
	}

	/** @inheritdoc */
	public function raw(string $sql, string|null $index = null, bool|null $assoc = null):Result{
		return $this->driver->raw($sql, $index, $assoc);
	}

	/** @inheritdoc */
	public function rawCached(string $sql, string|null $index = null, bool|null $assoc = null, int|null $ttl = null):Result{
		return $this->driver->rawCached($sql, $index, $assoc, $ttl);
	}

	/** @inheritdoc */
	public function prepared(string $sql, array|null $values = null, string|null $index = null, bool|null $assoc = null):Result{
		return $this->driver->prepared($sql, $values, $index, $assoc);
	}

	/** @inheritdoc */
	public function preparedCached(string $sql, array|null $values = null, string|null $index = null, bool|null $assoc = null, int|null $ttl = null):Result{
		return $this->driver->preparedCached($sql, $values, $index, $assoc, $ttl);
	}

	/** @inheritdoc */
	public function multi(string $sql, array $values):bool{
		return $this->driver->multi($sql, $values);
	}

	/** @inheritdoc */
	public function multiCallback(string $sql, array $data, callable|array $callback):bool{
		return $this->driver->multiCallback($sql, $data, $callback);
	}

	/** @inheritdoc */
	public function getDialect():Dialect{
		return $this->dialect;
	}

}
