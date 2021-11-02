<?php
/**
 * Class DBTestAbstract
 *
 * @created      21.04.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest;

use chillerlan\Database\DatabaseOptions;
use chillerlan\Database\Drivers\DriverInterface;
use chillerlan\DotEnv\DotEnv;
use chillerlan\Settings\SettingsContainerInterface;
use chillerlan\SimpleCache\MemoryCache;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Exception, ReflectionClass, ReflectionMethod;

use function constant, defined, realpath, str_replace;

use const DIRECTORY_SEPARATOR, PHP_OS_FAMILY;

abstract class DBTestAbstract extends TestCase{

	protected SettingsContainerInterface $options;
	protected DriverInterface $driver;
	protected CacheInterface $cache;
	protected LoggerInterface $logger;
	protected DotEnv $env;
	protected ReflectionClass $reflection;

	/**
	 * determines whether the tests run on Travis CI or GitHub Actions -> phpunit.xml TEST_IS_CI=TRUE
	 */
	protected bool $is_ci;

	/**
	 * the driver settings prefix in the .env file, e.g. MYSQL_HOST -> $env_prefix = MYSQL
	 */
	protected string $envPrefix;

	/**
	 * @throws \Exception
	 */
	protected function setUp():void{

		foreach(['TEST_CFGDIR', 'TEST_ENVFILE', 'TEST_IS_CI'] as $constant){
			if(!defined($constant)){
				throw new Exception($constant.' not set -> see phpunit.xml');
			}
		}

		// are we running on CI? (travis, github) -> see phpunit.xml
		$this->is_ci = constant('TEST_IS_CI') === true;

		// set the config dir and .env config
		$this->env    = new DotEnv(realpath(__DIR__.'/../'.constant('TEST_CFGDIR')), constant('TEST_ENVFILE'));
		$this->logger = new DBTestLogger($this->is_ci ? 'none' : 'debug');
		$this->cache  = new MemoryCache;

		$this->env->load();

		// fix database file names
		$this->env->set($this->envPrefix.'_DATABASE', str_replace(
			'{STORAGE}',
			realpath(__DIR__.'/../storage').DIRECTORY_SEPARATOR,
			$this->env->get($this->envPrefix.'_DATABASE')
		));


		$this->options = new DatabaseOptions([
			'host'     => $this->env->get($this->envPrefix.'_HOST'),
			'port'     => $this->env->get($this->envPrefix.'_PORT'),
			'database' => $this->env->get($this->envPrefix.'_DATABASE'),
			'username' => $this->env->get($this->envPrefix.'_USERNAME'),
			'password' => $this->env->get($this->envPrefix.'_PASSWORD'),
		]);

		$socket = $this->env->get($this->envPrefix.'_SOCKET');

		if(PHP_OS_FAMILY === 'Linux' && $socket !== null){
			$this->options->host = null;
			$this->options->port = null;
			$this->options->socket = $socket;
		}

	}

	/**
	 * Disconnet from the database on teardown
	 */
	protected function tearDown():void{

		if(isset($this->driver) && $this->driver instanceof DriverInterface){
			$this->driver->disconnect();
		}

#		if(isset($this->db) && $this->db instanceof Database){
#			$this->db->disconnect();
#		}

	}

	/**
	 *
	 */
	protected function getMethod($name):ReflectionMethod{
		$method = $this->reflection->getMethod($name);
		$method->setAccessible(true);

		return $method;
	}


}
