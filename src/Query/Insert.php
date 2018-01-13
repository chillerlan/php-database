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
 * @link https://www.sqlite.org/lang_insert.html
 * @link https://dev.mysql.com/doc/refman/5.7/en/insert.html
 * @link https://msdn.microsoft.com/library/ms174335(v=sql.110).aspx
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-dml-insert.html
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
	 *
	 * @return \chillerlan\Database\Query\Insert
	 */
	public function into(string $table, string $on_conflict = null);

	/**
	 * @param array $values
	 *
	 * @return \chillerlan\Database\Query\Insert
	 */
	public function values(array $values):Insert;

}
