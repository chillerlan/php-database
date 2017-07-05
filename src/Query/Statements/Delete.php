<?php
/**
 * Interface Delete
 *
 * @filesource   Delete.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

interface Delete extends Statement{

	/**
	 * @param string $table
	 *
	 * @return \chillerlan\Database\Query\Statements\Delete
	 */
	public function from(string $table):Delete;

	/**
	 * @param        $val1
	 * @param        $val2
	 * @param string $operator
	 * @param bool   $bind
	 * @param string $join
	 *
	 * @return \chillerlan\Database\Query\Statements\Delete
	 */
	public function where($val1, $val2, $operator = '=', $bind = true, $join = 'AND'):Delete;

}
