<?php
/**
 * Interface DropTable
 *
 * @filesource   DropTable.php
 * @created      07.01.2018
 * @package      chillerlan\Database\Query\Drop
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Drop;

use chillerlan\Database\Query\Statement;

/**
 * @link https://www.sqlite.org/lang_droptable.html
 * @link https://dev.mysql.com/doc/refman/5.7/en/drop-table.html
 */
interface DropTable extends Statement{

	/**
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\Drop\DropTable
	 */
	public function name(string $tablename);

	/**
	 * @return \chillerlan\Database\Query\Drop\DropTable
	 */
	public function ifExists();

}
