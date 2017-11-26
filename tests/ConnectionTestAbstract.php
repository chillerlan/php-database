<?php
/**
 * Class ConnectionTestAbstract
 *
 * @filesource   ConnectionTestAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest;

use chillerlan\Database\{Connection, Options};
use chillerlan\SimpleCache\Cache;
use chillerlan\SimpleCache\Drivers\MemoryCacheDriver;
use chillerlan\Traits\DotEnv;
use PHPUnit\Framework\TestCase;
use ReflectionClass, ReflectionMethod;

abstract class ConnectionTestAbstract extends TestCase{

	/**
	 * @var \Psr\SimpleCache\CacheInterface
	 */
	protected $cache;

	/**
	 * @var \chillerlan\Database\Options
	 */
	protected $options;

	/**
	 * @var \chillerlan\Database\Connection
	 */
	protected $connection;

	/**
	 * @var string (FQCN)
	 */
	protected $driver;

	/**
	 * @var string (FQCN)
	 */
	protected $querydriver;

	/**
	 * @var string config environment variable -> MYSQL_*
	 */
	protected $envVar;

	protected function setUp(){
		$env = (new DotEnv(__DIR__.'/../config', file_exists(__DIR__.'/../config/.env') ? '.env' : '.env_travis'))->load();

		$this->cache = new Cache(new MemoryCacheDriver);

		$this->options = new Options([
			'driver'       => $this->driver,
			'querybuilder' => $this->querydriver,
			'odbc_driver'  => $env->get($this->envVar.'ODBC_DRIVER'),
			'host'         => $env->get($this->envVar.'HOST'),
			'port'         => $env->get($this->envVar.'PORT'),
			'socket'       => $env->get($this->envVar.'SOCKET'),
			'database'     => $env->get($this->envVar.'DATABASE'),
			'username'     => $env->get($this->envVar.'USERNAME'),
			'password'     => $env->get($this->envVar.'PASSWORD'),
		]);

		$this->connection = new Connection($this->options, $this->cache);
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
		$this->assertInstanceOf(Connection::class, $this->connection);
	}
}
