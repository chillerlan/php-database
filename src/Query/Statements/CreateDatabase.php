<?php
/**
 * Interface CreateDatabase
 *
 * @filesource   CreateDatabase.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

interface CreateDatabase extends Statement{

	/**
	 * @return \chillerlan\Database\Query\Statements\CreateDatabase
	 */
	public function ifNotExists():CreateDatabase;

	/**
	 * @param string|null $dbname
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateDatabase
	 */
	public function name(string $dbname = null):CreateDatabase;

	/**
	 * @param string $collation
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateDatabase
	 */
	public function charset(string $collation):CreateDatabase;

}
