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

interface Query{

	public function sql(bool|null $multi = null):string;

	/**
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function query(string|null $index = null);

}
