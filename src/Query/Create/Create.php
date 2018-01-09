<?php
/**
 * Interface Create
 *
 * @filesource   Create.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Create
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Create;

use chillerlan\Database\Query\Statement;

/**
 * @link https://www.postgresql.org/docs/current/static/datatype.html
 * @link https://docs.microsoft.com/sql/t-sql/data-types/data-types-transact-sql
 */
interface Create extends Statement{

#	public function index():Create;
#	public function view():Create;
#	public function trigger():Create;

	/**
	 * @param string|null $dbname
	 *
	 * @return \chillerlan\Database\Query\Create\CreateDatabase
	 */
	public function database(string $dbname):CreateDatabase;

	/**
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\Create\CreateTable
	 */
	public function table(string $tablename):CreateTable;

}
