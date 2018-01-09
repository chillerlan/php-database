<?php
/**
 * Interface Insert
 *
 * @filesource   Insert.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Insert
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Insert;

use chillerlan\Database\Query\Statement;

/**
 * @link https://www.sqlite.org/lang_insert.html
 * @link https://dev.mysql.com/doc/refman/5.7/en/insert.html
 * @link https://msdn.microsoft.com/library/ms174335(v=sql.110).aspx
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-dml-insert.html
 */
interface Insert extends Statement{

	/**
	 * @param string $table
	 *
	 * @return \chillerlan\Database\Query\Insert\Insert
	 */
	public function into(string $table);

	/**
	 * @param array $values
	 *
	 * @return \chillerlan\Database\Query\Insert\Insert
	 */
	public function values(array $values):Insert;

}
