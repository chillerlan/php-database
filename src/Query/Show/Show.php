<?php
/**
 * Interface Show
 *
 * @filesource   Show.php
 * @created      09.01.2018
 * @package      chillerlan\Database\Query\Show
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Show;

use chillerlan\Database\Query\Statement;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/show.html
 */
interface Show extends Statement{

	public function createTable(string $tablename):Show;
	public function createDatabase(string $dbname):Show;
	public function createFunction(string $func):Show;
	public function createProcedure(string $proc):Show;
	public function databases():ShowDatabases;
	public function tables():ShowTables;
	public function columns():ShowColumns;
	public function index():ShowIndex;
	public function collation():ShowCollation;
	public function characterSet():ShowCharacterSet;
	public function tableStatus():ShowTableStatus;

}
