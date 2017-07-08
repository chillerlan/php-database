<?php
/**
 * Class Connection
 *
 * @filesource   Connection.php
 * @created      27.06.2017
 * @package      chillerlan\Database
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database;

use chillerlan\Database\Drivers\DriverInterface;
use chillerlan\Database\Query\{QueryBuilderInterface, QueryException};
use chillerlan\Database\Traits\ClassLoader;
use Psr\SimpleCache\CacheInterface;

/**
 * @property \chillerlan\Database\Query\Statements\Select $select
 * @property \chillerlan\Database\Query\Statements\Insert $insert
 * @property \chillerlan\Database\Query\Statements\Update $update
 * @property \chillerlan\Database\Query\Statements\Delete $delete
 * @property \chillerlan\Database\Query\Statements\Create $create
 * @property \chillerlan\Database\Query\Statements\Drop   $drop
 * @property \chillerlan\Database\Query\Statements\Alter  $alter
 */
class Connection implements DriverInterface{
	use ClassLoader;

	/**
	 * @var \chillerlan\Database\Options
	 */
	protected $options;

	/**
	 * @var \Psr\SimpleCache\CacheInterface
	 */
	protected $cache;

	/**
	 * @var \chillerlan\Database\Drivers\DriverInterface
	 */
	protected $driver;

	/**
	 * @var \chillerlan\Database\Query\QueryBuilderInterface
	 */
	protected $query;

	/**
	 * Connection constructor.
	 *
	 * @param \chillerlan\Database\Options         $options
	 * @param \Psr\SimpleCache\CacheInterface|null $cache
	 *
	 * @throws \chillerlan\Database\ConnectionException
	 */
	public function __construct(Options $options, CacheInterface $cache = null){
		$this->options = $options;
		$this->cache   = $cache;

		$this->driver = $this->loadClass($this->options->driver, DriverInterface::class, $this->options, $this->cache);

		if($this->options->querybuilder){
			$this->query = $this->loadClass($this->options->querybuilder, QueryBuilderInterface::class, $this->driver, $this->options);
		}
	}

	/**
	 * @param $name
	 *
	 * @return mixed
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function __get($name){
		$name = strtolower($name);

		if(in_array($name, QueryBuilderInterface::STATEMENTS)){
			return $this->query->{$name}();
		}

		throw new QueryException('invalid statement');
	}

	/**
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	public function __destruct(){
		$this->driver->disconnect();
	}

	/**
	 * @return \chillerlan\Database\Drivers\DriverInterface
	 */
	public function getDriver():DriverInterface{
		return $this->driver;
	}

	/**
	 * @return \chillerlan\Database\Query\QueryBuilderInterface|null
	 */
	public function getQueryBuilder(){
		return $this->query;
	}

	/**
	 * @inheritdoc
	 */
	public function connect():DriverInterface{
		return $this->driver->connect();
	}

	/**
	 * @inheritdoc
	 */
	public function disconnect():bool{
		return $this->driver->disconnect();
	}

	/**
	 * @inheritdoc
	 */
	public function getDBResource(){
		return $this->driver->getDBResource();
	}

	/**
	 * @inheritdoc
	 */
	public function getClientInfo():string{
		return $this->driver->getClientInfo();
	}

	/**
	 * @inheritdoc
	 */
	public function getServerInfo():string{
		return $this->driver->getServerInfo();
	}

	/**
	 * @inheritdoc
	 */
	public function escape($data){
		return $this->driver->escape($data);
	}

	/**
	 * @inheritdoc
	 */
	public function raw(string $sql, string $index = null, bool $assoc = true){
		return $this->driver->raw($sql, $index, $assoc);
	}

	/**
	 * @inheritdoc
	 */
	public function rawCached(string $sql, string $index = null, bool $assoc = true, int $ttl = null){
		return $this->driver->rawCached($sql, $index, $assoc, $ttl);
	}

	/**
	 * @inheritdoc
	 */
	public function prepared(string $sql, array $values = [], string $index = null, bool $assoc = true){
		return $this->driver->prepared($sql, $values, $index, $assoc);
	}

	/**
	 * @inheritdoc
	 */
	public function preparedCached(string $sql, array $values = [], string $index = null, bool $assoc = true, int $ttl = null){
		return $this->driver->preparedCached($sql, $values, $index, $assoc, $ttl);
	}

	/**
	 * @inheritdoc
	 */
	public function multi(string $sql, array $values){
		return $this->driver->multi($sql, $values);
	}

	/**
	 * @inheritdoc
	 */
	public function multiCallback(string $sql, array $data, $callback){
		return $this->driver->multiCallback($sql, $data, $callback);
	}

}
