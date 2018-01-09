<?php
/**
 * Interface Select
 *
 * @filesource   Select.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Select
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Select;

use chillerlan\Database\Query\Statement;

/**
 * @link https://www.sqlite.org/lang_select.html
 * @link https://dev.mysql.com/doc/refman/5.7/en/select.html
 * @link https://www.postgresql.org/docs/current/static/sql-select.html
 * @link https://msdn.microsoft.com/library/ms176104(v=sql.110).aspx
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-dml-select.html
 *
 * @method \chillerlan\Database\Query\Select\Select where($val1, $val2, string $operator = null, bool $bind = null, string $join = null)
 * @method \chillerlan\Database\Query\Select\Select openBracket(string $join = null)
 * @method \chillerlan\Database\Query\Select\Select closeBracket()
 */
interface Select extends Statement{

#	public function join():Select;
#	public function having():Select;
#	public function union():Select;

	/**
	 * @return \chillerlan\Database\Query\Select\Select
	 */
	public function distinct():Select;

	/**
	 * @param array $expressions
	 *
	 * @return \chillerlan\Database\Query\Select\Select
	 */
	public function cols(array $expressions):Select;

	/**
	 * @param array $expressions
	 *
	 * @return \chillerlan\Database\Query\Select\Select
	 */
	public function from(array $expressions):Select;

	/**
	 * @param array $expressions
	 *
	 * @return \chillerlan\Database\Query\Select\Select
	 */
	public function groupBy(array $expressions):Select;

	/**
	 * @param array $expressions
	 *
	 * @return \chillerlan\Database\Query\Select\Select
	 */
	public function orderBy(array $expressions):Select;

	/**
	 * @param int $offset
	 *
	 * @return \chillerlan\Database\Query\Select\Select
	 */
	public function offset(int $offset):Select;

	/**
	 * @param int $limit
	 *
	 * @return \chillerlan\Database\Query\Select\Select
	 */
	public function limit(int $limit):Select;

	/**
	 * @return int
	 */
	public function count():int;

	/**
	 * @param int|null $ttl
	 *
	 * @return \chillerlan\Database\Query\Select\Select
	 */
	public function cached(int $ttl = null):Select;

}
