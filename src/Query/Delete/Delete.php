<?php
/**
 * Interface Delete
 *
 * @filesource   Delete.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Delete
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Delete;

use chillerlan\Database\Query\Statement;

/**
 * @link https://www.sqlite.org/lang_delete.html
 * @link https://dev.mysql.com/doc/refman/5.7/en/delete.html
 * @link https://www.postgresql.org/docs/current/static/sql-delete.html
 * @link https://msdn.microsoft.com/de-de/library/ms189835(v=sql.110).aspx
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-dml-delete.html
 *
 * @method \chillerlan\Database\Query\Delete\Delete where($val1, $val2, string $operator = null, bool $bind = null, string $join = null)
 * @method \chillerlan\Database\Query\Delete\Delete openBracket(string $join = null)
 * @method \chillerlan\Database\Query\Delete\Delete closeBracket()
 */
interface Delete extends Statement{

	/**
	 * @param string $table
	 *
	 * @return \chillerlan\Database\Query\Delete\Delete
	 */
	public function from(string $table);

}
