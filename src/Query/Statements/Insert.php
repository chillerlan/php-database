<?php
/**
 * Interface Insert
 *
 * @filesource   Insert.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

interface Insert extends Statement{

	/**
	 * @param string $table
	 *
	 * @return \chillerlan\Database\Query\Statements\Insert
	 */
	public function into(string $table):Insert;

	/**
	 * @param array $values
	 *
	 * @return \chillerlan\Database\Query\Statements\Insert
	 */
	public function values(array $values):Insert;

}
