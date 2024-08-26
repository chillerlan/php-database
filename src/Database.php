<?php
/**
 * Class Database
 *
 * @created      27.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database;

use chillerlan\Database\Dialects\Dialect;
use chillerlan\Database\Drivers\DriverInterface;
use Closure;

class Database extends DatabaseAbstract implements DriverInterface{

	public function getDBResource():mixed{
		return $this->driver->getDBResource();
	}

	public function connect():DriverInterface{
		$this->driver->connect();

		return $this;
	}

	public function disconnect():bool{
		return $this->driver->disconnect();
	}

	public function getClientInfo():string{
		return $this->driver->getClientInfo();
	}

	public function getServerInfo():string{
		return $this->driver->getServerInfo();
	}

	public function escape(bool|float|int|string|null $data = null):float|int|string{
		return $this->driver->escape($data);
	}

	public function raw(string $sql, string|null $index = null, bool|null $assoc = null):Result{
		return $this->driver->raw($sql, $index, $assoc);
	}

	public function rawCached(string $sql, string|null $index = null, bool|null $assoc = null, int|null $ttl = null):Result{
		return $this->driver->rawCached($sql, $index, $assoc, $ttl);
	}

	public function prepared(string $sql, array|null $values = null, string|null $index = null, bool|null $assoc = null):Result{
		return $this->driver->prepared($sql, $values, $index, $assoc);
	}

	public function preparedCached(string $sql, array|null $values = null, string|null $index = null, bool|null $assoc = null, int|null $ttl = null):Result{
		return $this->driver->preparedCached($sql, $values, $index, $assoc, $ttl);
	}

	public function multi(string $sql, array $values):bool{
		return $this->driver->multi($sql, $values);
	}

	public function multiCallback(string $sql, array $data, Closure $callback):bool{
		return $this->driver->multiCallback($sql, $data, $callback);
	}

	public function getDialect():Dialect{
		return $this->dialect;
	}

}
