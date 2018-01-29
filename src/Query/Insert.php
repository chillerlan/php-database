<?php
/**
 * Interface Insert
 *
 * @filesource   Insert.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/insert.html
 * @link https://www.postgresql.org/docs/current/static/sql-insert.html
 * @link https://docs.microsoft.com/en-gb/sql/t-sql/statements/insert-transact-sql
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-dml-insert.html
 * @link https://www.sqlite.org/lang_insert.html
 *
 * @method string sql(bool $multi = null)
 * @method array  getBindValues()
 * @method mixed  query(string $index = null)
 * @method mixed  multi(iterable $values = null)
 * @method callback(iterable $values, \Closure $callback)
 */
interface Insert extends Statement{

	/**
	 * @param string      $table
	 * @param string|null $on_conflict
	 * @param string|null $conflict_target
	 *
	 * @return \chillerlan\Database\Query\Insert
	 */
	public function into(string $table, string $on_conflict = null, string $conflict_target = null);

	/**
	 * @param iterable $values
	 *
	 * @return \chillerlan\Database\Query\Insert
	 */
	public function values(iterable $values):Insert;

}
