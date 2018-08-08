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
use chillerlan\Traits\{
	ClassLoader, ImmutableSettingsInterface
};
use Psr\Log\{
	LoggerAwareInterface, LoggerAwareTrait, LoggerInterface, NullLogger
};
use Psr\SimpleCache\CacheInterface;

abstract class DatabaseAbstract implements LoggerAwareInterface{
	use ClassLoader, LoggerAwareTrait;

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
	 * @param \chillerlan\Traits\ImmutableSettingsInterface $options
	 * @param \Psr\SimpleCache\CacheInterface|null $cache
	 * @param \Psr\Log\LoggerInterface|null        $logger
	 */
	public function __construct(ImmutableSettingsInterface $options, CacheInterface $cache = null, LoggerInterface $logger = null){
		$this->options = $options;
		$this->cache   = $cache;

		// set a default logger
		$this->logger  = $logger ?? new NullLogger;

		$this->driver  = $this->loadClass($this->options->driver, DriverInterface::class, $this->options, $this->cache, $this->logger);
		$this->query   = new QueryBuilder($this->driver, $this->logger);
	}

	/**
	 * @param \Psr\Log\LoggerInterface $logger
	 *
	 * @return void
	 */
	public function setLogger(LoggerInterface $logger):void{
		$this->logger = $logger;
		$this->driver->setLogger($logger);
		$this->query->setLogger($logger);
	}

}
