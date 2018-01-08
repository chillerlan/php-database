<?php
/**
 * Interface DropDatabase
 *
 * @filesource   DropDatabase.php
 * @created      07.01.2018
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

interface DropDatabase extends Statement{

	/**
	 * @param string $dbname
	 *
	 * @return \chillerlan\Database\Query\Statements\DropDatabase
	 */
	public function name(string $dbname):DropDatabase;

	/**
	 * @return \chillerlan\Database\Query\Statements\DropDatabase
	 */
	public function ifExists():DropDatabase;

}
