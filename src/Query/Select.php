<?php
/**
 * Interface Select
 *
 * @filesource   Select.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

/**
 * @link https://www.sqlite.org/lang_select.html
 * @link https://dev.mysql.com/doc/refman/5.7/en/select.html
 * @link https://www.postgresql.org/docs/current/static/sql-select.html
 * @link https://msdn.microsoft.com/library/ms176104(v=sql.110).aspx
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-dml-select.html
 *
 * @method \chillerlan\Database\Query\Select where($val1, $val2, string $operator = null, bool $bind = null, string $join = null)
 * @method \chillerlan\Database\Query\Select openBracket(string $join = null)
 * @method \chillerlan\Database\Query\Select closeBracket()
 * @method \chillerlan\Database\Query\Select limit(int $limit)
 * @method \chillerlan\Database\Query\Select offset(int $offset)
 * @method \chillerlan\Database\Query\Select cached(int $ttl = null)
 * @method string sql(bool $multi = null)
 * @method array  getBindValues()
 * @method mixed  query(string $index = null)
 */
interface Select extends Statement{

#	public function join():Select;
#	public function having():Select;
#	public function union():Select;

	/**
	 * @return \chillerlan\Database\Query\Select
	 */
	public function distinct():Select;

	/**
	 * @param array $expressions
	 *
	 * @return \chillerlan\Database\Query\Select
	 */
	public function cols(array $expressions):Select;

	/**
	 * @param array $expressions
	 *
	 * @return \chillerlan\Database\Query\Select
	 */
	public function from(array $expressions):Select;

	/**
	 * @param array $expressions
	 *
	 * @return \chillerlan\Database\Query\Select
	 */
	public function groupBy(array $expressions):Select;

	/**
	 * @param array $expressions
	 *
	 * @return \chillerlan\Database\Query\Select
	 */
	public function orderBy(array $expressions):Select;

	/**
	 * @return int
	 */
	public function count():int;


}
