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
use Dotenv\Dotenv;
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
		(new Dotenv(__DIR__.'/../config', '.env_travis'))->load();

		$this->cache = new Cache(new MemoryCacheDriver);

		$this->options = new Options([
			'driver'       => $this->driver,
			'querybuilder' => $this->querydriver,
			'odbc_driver'  => getenv($this->envVar.'ODBC_DRIVER'),
			'host'         => getenv($this->envVar.'HOST'),
			'port'         => getenv($this->envVar.'PORT'),
			'socket'       => getenv($this->envVar.'SOCKET'),
			'database'     => getenv($this->envVar.'DATABASE'),
			'username'     => getenv($this->envVar.'USERNAME'),
			'password'     => getenv($this->envVar.'PASSWORD'),
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
