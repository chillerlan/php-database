<?php
/**
 * Class DatabaseTestAbstract
 *
 * @filesource   DatabaseTestAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest;

use chillerlan\Database\{Database, DatabaseOptions};
use chillerlan\SimpleCache\Cache;
use chillerlan\SimpleCache\Drivers\MemoryCacheDriver;
use chillerlan\Traits\DotEnv;
use PHPUnit\Framework\TestCase;
use ReflectionClass, ReflectionMethod;

abstract class DatabaseTestAbstract extends TestCase{

	/**
	 * @var \Psr\SimpleCache\CacheInterface
	 */
	protected $cache;

	/**
	 * @var \chillerlan\Database\DatabaseOptions
	 */
	protected $options;

	/**
	 * @var \chillerlan\Database\Database
	 */
	protected $connection;

	/**
	 * @var string (FQCN)
	 */
	protected $driver;

	/**
	 * @var string config environment variable -> MYSQL_*
	 */
	protected $envVar;

	protected function setUp(){
		$env = (new DotEnv(__DIR__.'/../config', file_exists(__DIR__.'/../config/.env') ? '.env' : '.env_travis'))->load();

		$this->cache = new Cache(new MemoryCacheDriver);

		$this->options = new DatabaseOptions([
			'driver'       => $this->driver,
#			'odbc_driver'  => $env->get($this->envVar.'ODBC_DRIVER'),
			'host'         => $env->get($this->envVar.'HOST'),
			'port'         => $env->get($this->envVar.'PORT'),
			'socket'       => $env->get($this->envVar.'SOCKET'),
			'database'     => $env->get($this->envVar.'DATABASE'),
			'username'     => $env->get($this->envVar.'USERNAME'),
			'password'     => $env->get($this->envVar.'PASSWORD'),
		]);

		$this->connection = new Database($this->options, $this->cache);
		$this->connection->connect();
	}

	protected function tearDown(){
		$this->assertTrue($this->connection->disconnect());
		// try to disconnect from a disconnected DB (should not throw errors -> mysqli)
		$this->assertTrue($this->connection->disconnect());
	}

	protected function getMethod($class, $name):ReflectionMethod{
		$class = new ReflectionClass($class);
		$method = $class->getMethod($name);
		$method->setAccessible(true);

		return $method;
	}

	public function testConnectionInstance(){
		$this->assertInstanceOf(Database::class, $this->connection);
	}
}
