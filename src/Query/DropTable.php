<?php
/**
 * Interface DropTable
 *
 * @filesource   DropTable.php
 * @created      07.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

/**
 * @link https://www.sqlite.org/lang_droptable.html
 * @link https://dev.mysql.com/doc/refman/5.7/en/drop-table.html
 *
 * @method string sql(bool $multi = null)
 * @method mixed  query(string $index = null)
 */
interface DropTable extends Statement{

	/**
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\DropTable
	 */
	public function name(string $tablename);

	/**
	 * @return \chillerlan\Database\Query\DropTable
	 */
	public function ifExists();

}
