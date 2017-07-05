<?php
/**
 *
 * @filesource   Create.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

/**
 * Interface Create
 */
interface Create extends Statement{

	/**
	 * @param string|null $dbname
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateDatabase
	 */
	public function database(string $dbname = null):CreateDatabase;

	/**
	 * @param string|null $tablename
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function table(string $tablename = null):CreateTable;

#	public function index():Create;
#	public function view():Create;
#	public function trigger():Create;

}
