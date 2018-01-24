<?php
/**
 * Interface DriverInterface
 *
 * @filesource   DriverInterface.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\{
	DatabaseOptions, Dialects\Dialect
};
use chillerlan\Traits\ContainerInterface;
use Psr\{
	Log\LoggerInterface, SimpleCache\CacheInterface
};

interface DriverInterface{

	/**
	 * Constructor.
	 *
	 * @param \chillerlan\Traits\ContainerInterface $options
	 * @param \Psr\SimpleCache\CacheInterface|null  $cache
	 * @param \Psr\Log\LoggerInterface|null         $logger
	 */
	public function __construct(ContainerInterface $options, CacheInterface $cache = null, LoggerInterface $logger = null);

	/**
	 * Establishes a database connection and returns the connection object
	 *
	 * @return \chillerlan\Database\Drivers\DriverInterface
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	public function connect():DriverInterface;

	/**
	 * Closes a database connection
	 *
	 * @return bool
	 */
	public function disconnect():bool;

	/**
	 * Returns the connection object
	 *
	 * @return mixed the database resource object
	 */
	public function getDBResource();

	/**
	 * @return \chillerlan\Database\Dialects\Dialect
	 */
	public function getDialect():Dialect;

	/**
	 * Returns info about the used php client
	 *
	 * @return string php's database client string
	 */
	public function getClientInfo():string;

	/**
	 * Returns info about the database server
	 *
	 * @return string serverinfo string
	 */
	public function getServerInfo():?string;

	/**
	 * Sanitizer.
	 *
	 * @param mixed $data string to escape
	 *
	 * @return mixed escaped. obviously.
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
	 * @param string $sql         The SQL statement
	 * @param string $index       [optional] an index column to assingn as the result's keys
	 * @param bool   $assoc       [optional] If true, the fields are named with the respective column names, otherwise numbered
	 *
	 * @return \chillerlan\Database\Result|bool array with results, true on void query success, otherwise false.
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	public function raw(string $sql, string $index = null, bool $assoc = null);

	/**
	 * same as DriverInterface::raw(), but cached.
	 *
	 * @param string      $sql
	 * @param string|null $index
	 * @param bool        $assoc
	 * @param int|null    $ttl
	 *
	 * @return \chillerlan\Database\Result|bool
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	public function rawCached(string $sql, string $index = null, bool $assoc = null, int $ttl = null);

	/**
	 * Prepared statements wrapper
	 *
	 * Does everything for you: prepares the statement and fetches the results as an object or array
	 * just pass a query along with values and you're done. Not meant for multi-inserts.
	 *
	 * @param string $sql         The SQL statement to prepare
	 * @param array  $values      [optional] the value for each parameter in the statement - in the respective order, of course
	 * @param string $index       [optional] an index column to assingn as the result's keys
	 * @param bool   $assoc       [optional] If true, the fields are named with the respective column names, otherwise numbered
	 *
	 * @return \chillerlan\Database\Result|bool Array with results, true on void query success, otherwise false
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	public function prepared(string $sql, array $values = null, string $index = null, bool $assoc = null);

	/**
	 * same as DriverInterface::prepared(), but cached.
	 *
	 * @param string      $sql
	 * @param array       $values
	 * @param string|null $index
	 * @param bool        $assoc
	 * @param int|null    $ttl
	 *
	 * @return \chillerlan\Database\Result|bool
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	public function preparedCached(string $sql, array $values = null, string $index = null, bool $assoc = null, int $ttl = null);

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
	public function multi(string $sql, array $values);

	/**
	 * Prepared multi line insert/update with callback
	 *
	 * multi threading? - conclusion after 2 years: nah, mysql is the bottleneck.
	 * @link https://gist.github.com/krakjoe/6437782
	 * @link https://gist.github.com/krakjoe/9384409
	 *
	 * @param string         $sql      The SQL statement to prepare
	 * @param iterable       $data     an array with the (raw) data to insert, each row represents one line to insert.
	 * @param callable|array $callback a callback that processes the values for each row.
	 *
	 * @return bool true query success, otherwise false
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	public function multiCallback(string $sql, iterable $data, $callback);

}
