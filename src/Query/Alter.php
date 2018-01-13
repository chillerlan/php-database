<?php
/**
 * Interface Alter
 *
 * @filesource   Alter.php
 * @created      15.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

interface Alter extends Statement{

	/**
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\AlterTable
	 */
	public function table(string $tablename):AlterTable;

	/**
	 * @param string $dbname
	 *
	 * @return \chillerlan\Database\Query\AlterDatabase
	 */
	public function database(string $dbname):AlterDatabase;

}
