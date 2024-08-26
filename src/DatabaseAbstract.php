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
use chillerlan\Database\Query\{Alter, Create, Delete, Drop, Insert, QueryException, Select, Show, Statement, Truncate, Update};
use chillerlan\Settings\SettingsContainerInterface;
use Psr\Log\{LoggerInterface, NullLogger};
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
abstract class DatabaseAbstract{

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

	protected SettingsContainerInterface|DatabaseOptions $options;
	protected CacheInterface|null                        $cache = null;
	protected LoggerInterface                            $logger;
	protected DriverInterface                            $driver;
	protected Dialect                                    $dialect;

	/**
	 * Database constructor.
	 *
	 * @throws \chillerlan\Database\DatabaseException
	 */
	public function __construct(
		SettingsContainerInterface|DatabaseOptions $options,
		CacheInterface|null                        $cache = null,
		LoggerInterface                            $logger = new NullLogger,
	){
		$this->options = $options;
		$this->cache   = $cache;
		$this->logger  = $logger;

		$this->driver  = new ($this->options->driver)($this->options, $this->cache, $this->logger);
		$this->dialect = $this->driver->getDialect();
	}

	/**
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function __get(string $name):Statement{
		$name = strtolower($name);

		if(isset($this::STATEMENTS[$name])){
			return new ($this::STATEMENTS[$name])($this->driver, $this->dialect, $this->logger);
		}

		throw new QueryException('invalid statement');
	}

	public function __destruct(){
		$this->driver->disconnect();
	}

	public function getDriver():DriverInterface{
		return $this->driver;
	}

	public function setLogger(LoggerInterface $logger):static{
		$this->logger = $logger;
		$this->driver->setLogger($logger);

		return $this;
	}

}
