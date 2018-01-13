<?php
/**
 * Interface MultiQuery
 *
 * @filesource   MultiQuery.php
 * @created      13.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use Closure;

interface MultiQuery extends Query{

	/**
	 * @param iterable|null $values
	 *
	 * @return mixed
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function multi(iterable $values = null);

	/**
	 * @param iterable $values
	 * @param \Closure $callback
	 *
	 * @return mixed
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function callback(iterable $values, Closure $callback);

}
