<?php
/**
 * Interface Query
 *
 * @filesource   Query.php
 * @created      10.01.2018
 * @package      chillerlan\Database\Query
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
	 * @return array
	 */
	public function getBindValues():array;

	/**
	 * @param int $limit
	 *
	 * @return mixed
	 */
	public function limit(int $limit);

	/**
	 * @param int $offset
	 *
	 * @return mixed
	 */
	public function offset(int $offset);

	/**
	 * @param int|null $ttl
	 *
	 * @return $this
	 */
	public function cached(int $ttl = null);

	/**
	 * @param string|null $index
	 *
	 * @return \chillerlan\Database\Result|bool
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function query(string $index = null);

}
