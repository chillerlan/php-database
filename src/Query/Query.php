<?php
/**
 * Interface Query
 *
 * @created      10.01.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

interface Query{

	/**
	 * @param bool|null $multi
	 *
	 * @return string
	 */
	public function sql(bool $multi = null):string;

	/**
	 * @param string|null $index
	 *
	 * @return \chillerlan\Database\Result|bool
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function query(string $index = null);

}
