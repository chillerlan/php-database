<?php
/**
 * Interface Drop
 *
 * @filesource   Drop.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

interface Drop extends Statement{

	/**
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\DropTable
	 */
	public function table(string $tablename):DropTable;

	/**
	 * @param string $dbname
	 *
	 * @return \chillerlan\Database\Query\DropDatabase
	 */
	public function database(string $dbname):DropDatabase;

}
