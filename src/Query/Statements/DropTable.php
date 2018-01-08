<?php
/**
 * Interface DropTable
 *
 * @filesource   DropTable.php
 * @created      07.01.2018
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

interface DropTable extends Statement{

	/**
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\Statements\DropTable
	 */
	public function name(string $tablename):DropTable;

	/**
	 * @return \chillerlan\Database\Query\Statements\DropTable
	 */
	public function ifExists():DropTable;

}
