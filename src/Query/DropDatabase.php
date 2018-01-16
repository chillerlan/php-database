<?php
/**
 * Interface DropDatabase
 *
 * @filesource   DropDatabase.php
 * @created      07.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/drop-database.html
 *
 * @method string sql(bool $multi = null)
 * @method mixed  query(string $index = null)
 */
interface DropDatabase extends Statement{

	/**
	 * @param string $dbname
	 *
	 * @return \chillerlan\Database\Query\DropDatabase
	 */
	public function name(string $dbname);

	/**
	 * @return \chillerlan\Database\Query\DropDatabase
	 */
	public function ifExists();

}
