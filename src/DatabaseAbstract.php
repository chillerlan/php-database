<?php
/**
 * Class DatabaseAbstract
 *
 * @created      20.01.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database;

use chillerlan\Database\Dialects\Dialect;
use chillerlan\Database\Drivers\DriverInterface;
use chillerlan\Database\Query\{Alter, Create, Delete, Drop, Insert, QueryException, Select, Show, Truncate, Update};
use chillerlan\Settings\SettingsContainerInterface;
use Psr\Log\{LoggerAwareInterface, LoggerAwareTrait, LoggerInterface, NullLogger};
use Psr\SimpleCache\CacheInterface;

use function strtolower;

/**
 * @property \chillerlan\Database\Query\Alter    $alter
 * @property \chillerlan\Database\Query\Create   $create
 * @property \chillerlan\Database\Query\Delete   $delete
 * @property \chillerlan\Database\Query\Drop     $drop
 * @property \chillerlan\Database\Query\Insert   $insert
 * @property \chillerlan\Database\Query\Select   $select
 * @property \chillerlan\Database\Query\Show     $show
 * @property \chillerlan\Database\Query\Truncate $truncate
 * @property \chillerlan\Database\Query\Update   $update
 */
abstract class DatabaseAbstract implements LoggerAwareInterface{
	use LoggerAwareTrait;

	protected const STATEMENTS = [
		'alter'    => Alter::class,
		'create'   => Create::class,
		'delete'   => Delete::class,
		'drop'     => Drop::class,
		'insert'   => Insert::class,
		'select'   => Select::class,
		'show'     => Show::class,
		'truncate' => Truncate::class,
		'update'   => Update::class,
	];
	/** @var \chillerlan\Database\DatabaseOptions */
	protected SettingsContainerInterface $options;
	protected ?CacheInterface $cache = null;
	protected DriverInterface $driver;
	protected Dialect $dialect;

	/**
	 * Database constructor.
	 *
	 * @throws \chillerlan\Database\DatabaseException
	 */
	public function __construct(SettingsContainerInterface $options, CacheInterface $cache = null, LoggerInterface $logger = null){
		$this->options = $options;
		$this->cache   = $cache;

		// set a default logger
		$this->logger  = $logger ?? new NullLogger;
		/** @phan-suppress-next-line PhanTypeExpectedObjectOrClassName */
		$this->driver  = new $this->options->driver($this->options, $this->cache, $this->logger);

		if(!$this->driver instanceof DriverInterface){
			throw new DatabaseException('invalid driver interface');
		}

		$this->dialect = $this->driver->getDialect();
	}

	/**
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function __get(string $name){
		$name = strtolower($name);

		if(isset($this::STATEMENTS[$name])){
			$statement = $this::STATEMENTS[$name];

			return new $statement($this->driver, $this->dialect, $this->logger);
		}

		throw new QueryException('invalid statement');
	}

	/**
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
	 *
	 */
	public function setLogger(LoggerInterface $logger):void{
		$this->logger = $logger;
		$this->driver->setLogger($logger);
	}

}
