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
use chillerlan\Settings\SettingsContainerInterface;
use Psr\Log\{
	LoggerAwareInterface, LoggerAwareTrait, LoggerInterface, NullLogger
};
use Psr\SimpleCache\CacheInterface;

abstract class DatabaseAbstract implements LoggerAwareInterface{
	use LoggerAwareTrait;

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
	 * @param \chillerlan\Settings\SettingsContainerInterface $options
	 * @param \Psr\SimpleCache\CacheInterface|null $cache
	 * @param \Psr\Log\LoggerInterface|null        $logger
	 *
	 * @throws \chillerlan\Database\DatabaseException
	 */
	public function __construct(SettingsContainerInterface $options, CacheInterface $cache = null, LoggerInterface $logger = null){
		$this->options = $options;
		$this->cache   = $cache;

		// set a default logger
		$this->logger  = $logger ?? new NullLogger;
		$this->driver  = new $this->options->driver($this->options, $this->cache, $this->logger);

		if(!$this->driver instanceof DriverInterface){
			throw new DatabaseException('invalid driver interface');
		}

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
