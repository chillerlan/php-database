<?php
/**
 * Interface MultiQuery
 *
 * @created      13.01.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use Closure;

interface MultiQuery extends Query{

	/**
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function multi(array|null $values = null):bool;

	/**
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function callback(array $values, Closure $callback):bool;

}
