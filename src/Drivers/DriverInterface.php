<?php
/**
 * Interface DriverInterface
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Dialects\Dialect;
use chillerlan\Database\Result;
use chillerlan\Settings\SettingsContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

interface DriverInterface{

	/**
	 * Constructor.
	 */
	public function __construct(SettingsContainerInterface $options, CacheInterface $cache = null, LoggerInterface $logger = null);

	/**
	 * Establishes a database connection
	 *
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	public function connect():DriverInterface;

	/**
	 * Closes a database connection
	 */
	public function disconnect():bool;

	/**
	 * Returns the database resource object
	 *
	 * @return mixed (resource, PDO, mysqli, ...)
	 */
	public function getDBResource();

	/**
	 * Returns an SQL dialect object for the current driver
	 */
	public function getDialect():Dialect;

	/**
	 * Returns info about the used php client
	 */
	public function getClientInfo():string;

	/**
	 * Returns info about the database server
	 */
	public function getServerInfo():string;

	/**
	 * Sanitizer.
	 *
	 * @param mixed $data string to escape
	 *
	 * @return mixed escaped. obviously. (hopefully)
	 */
	public function escape($data = null);

	/**
	 * Basic SQL query for non prepared statements
	 *
	 * There is no escaping in here, so make sure, your SQL is clean/escaped.
	 * Also, your SQL should NEVER contain user input, use prepared statements in this case.
	 *
	 * If the query was successful it returns either an array of results or true
	 * if it was a void query. On errors, a false will be returned, obviously.
	 *
	 * @param string      $sql    The SQL statement
	 * @param string|null $index  [optional] an index column to assingn as the result's keys
	 * @param bool|null   $assoc  [optional] If true, the fields are named with the respective column names, otherwise numbered
	 *
	 * @return \chillerlan\Database\Result array with results
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	public function raw(string $sql, string $index = null, bool $assoc = null):Result;

	/**
	 * same as DriverInterface::raw(), but cached.
	 *
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	public function rawCached(string $sql, string $index = null, bool $assoc = null, int $ttl = null):Result;

	/**
	 * Prepared statements wrapper
	 *
	 * Does everything for you: prepares the statement and fetches the results as an object or array
	 * just pass a query along with values and you're done. Not meant for multi-inserts.
	 *
	 * @param string      $sql     The SQL statement to prepare
	 * @param array|null  $values  [optional] the value for each parameter in the statement - in the respective order, of course
	 * @param string|null $index   [optional] an index column to assingn as the result's keys
	 * @param bool|null   $assoc   [optional] If true, the fields are named with the respective column names, otherwise numbered
	 *
	 * @return \chillerlan\Database\Result Array with results
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	public function prepared(string $sql, array $values = null, string $index = null, bool $assoc = null):Result;

	/**
	 * same as DriverInterface::prepared(), but cached.
	 *
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	public function preparedCached(string $sql, array $values = null, string $index = null, bool $assoc = null, int $ttl = null):Result;

	/**
	 * Prepared multi line insert
	 *
	 * Prepared statement multi insert/update
	 *
	 * @param string   $sql    The SQL statement to prepare
	 * @param array    $values a multidimensional array with the values, each row represents one line to insert.
	 *
	 * @return bool true query success, otherwise false
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	public function multi(string $sql, array $values):bool;

	/**
	 * Prepared multi line insert/update with callback
	 *
	 * multi threading? - conclusion after 2 years: nah, mysql is the bottleneck.
	 * @link https://gist.github.com/krakjoe/6437782
	 * @link https://gist.github.com/krakjoe/9384409
	 *
	 * @param string         $sql      The SQL statement to prepare
	 * @param array          $data     an array with the (raw) data to insert, each row represents one line to insert.
	 * @param callable|array $callback a callback that processes the values for each row.
	 *
	 * @return bool true query success, otherwise false
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	public function multiCallback(string $sql, array $data, $callback):bool;

}
