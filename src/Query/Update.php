<?php
/**
 * Interface Update
 *
 * @filesource   Update.php
 * @created      03.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

/**
 * @link https://www.sqlite.org/lang_update.html
 * @link https://dev.mysql.com/doc/refman/5.7/en/update.html
 * @link https://www.postgresql.org/docs/current/static/sql-update.html
 * @link https://msdn.microsoft.com/library/ms177523(v=sql.110).aspx
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-dml-update.html
 *
 * @method \chillerlan\Database\Query\Update where($val1, $val2, string $operator = null, bool $bind = null, string $join = null)
 * @method \chillerlan\Database\Query\Update openBracket(string $join = null)
 * @method \chillerlan\Database\Query\Update closeBracket()
 * @method string sql(bool $multi = null)
 * @method array  getBindValues()
 * @method mixed  query(string $index = null)
 * @method mixed  multi(iterable $values = null)
 * @method callback(iterable $values, \Closure $callback)
 */
interface Update extends Statement{

	/**
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\Update
	 */
	public function table(string $tablename);

	/**
	 * @param array $set
	 * @param bool  $bind
	 *
	 * @return \chillerlan\Database\Query\Update
	 */
	public function set(array $set, bool $bind = null):Update;

}
