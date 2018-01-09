<?php
/**
 * Interface Statement
 *
 * @filesource   Statement.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use Closure;
use Psr\Log\LoggerAwareInterface;

interface Statement extends LoggerAwareInterface{

	/**
	 * @return string
	 * @throws \chillerlan\Database\Query\StatementException
	 */
	public function sql():string;

	/**
	 * @return array
	 */
	public function bindValues():array;

	/**
	 * @param string|null $index
	 *
	 * @return \chillerlan\Database\Result|bool
	 * @throws \chillerlan\Database\Query\StatementException
	 */
	public function query(string $index = null);

	/**
	 * @param iterable|null $values
	 *
	 * @return mixed
	 * @throws \chillerlan\Database\Query\StatementException
	 */
	public function multi(iterable $values = null);

	/**
	 * @param iterable $values
	 * @param \Closure $callback
	 *
	 * @return mixed
	 * @throws \chillerlan\Database\Query\StatementException
	 */
	public function callback(iterable $values, Closure $callback);

}
