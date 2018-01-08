<?php
/**
 * Interface Drop
 *
 * @filesource   Drop.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

interface Drop extends Statement{

	/**
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\Statements\DropTable
	 */
	public function table(string $tablename):DropTable;

	/**
	 * @param string $dbname
	 *
	 * @return \chillerlan\Database\Query\Statements\DropDatabase
	 */
	public function database(string $dbname):DropDatabase;

}
