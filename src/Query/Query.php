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

	/**
	 * @param bool|null $multi
	 *
	 * @return string
	 */
	public function sql(bool|null $multi = null):string;

	/**
	 * @param string|null $index
	 *
	 * @return \chillerlan\Database\Result|bool
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function query(string|null $index = null);

}
