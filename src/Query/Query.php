<?php
/**
 * Interface Query
 *
 * @created      10.01.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

interface Query extends Statement{

	/**
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function executeQuery(string|null $index = null);

}
