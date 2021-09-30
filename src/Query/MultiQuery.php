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

interface MultiQuery extends Query{

	/**
	 * @param array|null $values
	 *
	 * @return mixed
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function multi(array $values = null);

	/**
	 * @param iterable $values
	 * @param callable $callback
	 *
	 * @return mixed
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function callback(array $values, callable $callback);

}
