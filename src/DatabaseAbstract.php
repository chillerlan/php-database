<?php
/**
 * Class DatabaseAbstract
 *
 * @filesource   DatabaseAbstract.php
 * @created      20.01.2018
 * @package      chillerlan\Database
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database;

use chillerlan\Database\{
	Drivers\DriverInterface, Query\QueryBuilder
};
use chillerlan\{
	Logger\LogTrait, Traits\ClassLoader, Traits\ContainerInterface
};
use Psr\{
	Log\LoggerAwareInterface, Log\LoggerInterface, SimpleCache\CacheInterface
};

abstract class DatabaseAbstract implements LoggerAwareInterface{
	use ClassLoader, LogTrait;

	/**
	 * @var \chillerlan\Database\DatabaseOptions
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
	 * @var \chillerlan\Database\Query\QueryBuilder
	 */
	protected $query;

	/**
	 * Database constructor.
	 *
	 * @param \chillerlan\Traits\ContainerInterface $options
	 * @param \Psr\SimpleCache\CacheInterface|null $cache
	 * @param \Psr\Log\LoggerInterface|null        $logger
	 */
	public function __construct(ContainerInterface $options, CacheInterface $cache = null, LoggerInterface $logger = null){
		$this->options = $options;
		$this->cache   = $cache;
		$this->driver  = $this->loadClass($this->options->driver, DriverInterface::class, $this->options, $this->cache, $this->log);
		$this->query   = new QueryBuilder($this->driver, $this->log);

		if($logger instanceof LoggerInterface){
			$this->setLogger($logger);
		}

	}

	/**
	 * @param \Psr\Log\LoggerInterface $logger
	 *
	 * @return \chillerlan\Database\Database
	 */
	public function setLogger(LoggerInterface $logger):DatabaseAbstract{
		$this->log = $logger;
		$this->driver->setLogger($logger);
		$this->query->setLogger($logger);

		return $this;
	}


}
