<?php
/**
 * Interface MultiQuery
 *
 * @created      13.01.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

use Closure;

interface MultiQuery extends Query{

	/**
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function executeMultiQuery(array|null $values = null, Closure|null $callback = null):bool;

}
