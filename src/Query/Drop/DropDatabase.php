<?php
/**
 * Interface DropDatabase
 *
 * @filesource   DropDatabase.php
 * @created      07.01.2018
 * @package      chillerlan\Database\Query\Drop
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Drop;

use chillerlan\Database\Query\Statement;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/drop-database.html
 */
interface DropDatabase extends Statement{

	/**
	 * @param string $dbname
	 *
	 * @return \chillerlan\Database\Query\Drop\DropDatabase
	 */
	public function name(string $dbname);

	/**
	 * @return \chillerlan\Database\Query\Drop\DropDatabase
	 */
	public function ifExists();

}
