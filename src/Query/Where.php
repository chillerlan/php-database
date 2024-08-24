<?php
/**
 * Interface Where
 *
 * @created      09.01.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/where-optimization.html
 * @link https://www.postgresql.org/docs/current/static/sql-select.html#SQL-WHERE
 * @link https://docs.microsoft.com/de-de/sql/t-sql/queries/where-transact-sql
 */
interface Where{

	/**
	 * @param mixed       $val1
	 * @param mixed       $val2
	 * @param string|null $operator
	 * @param bool|null   $bind
	 * @param string|null $join
	 */
	public function where(mixed $val1, mixed $val2 = null, string|null $operator = null, bool|null $bind = null, string|null $join = null):static;

	public function openBracket(string|null $join = null):static;

	public function closeBracket():static;

}
