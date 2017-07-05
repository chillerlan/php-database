<?php
/**
 * Interface Update
 *
 * @filesource   Update.php
 * @created      03.06.2017
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

interface Update extends Statement{

	/**
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\Statements\Update
	 */
	public function table(string $tablename):Update;

	/**
	 * @param array $set
	 * @param bool  $bind
	 *
	 * @return \chillerlan\Database\Query\Statements\Update
	 */
	public function set(array $set, bool $bind = true):Update;

	/**
	 * @param        $val1
	 * @param        $val2
	 * @param string $operator
	 * @param bool   $bind
	 * @param string $join
	 *
	 * @return \chillerlan\Database\Query\Statements\Update
	 */
	public function where($val1, $val2, $operator = '=', $bind = true, $join = 'AND'):Update;

	/**
	 * @param null $join
	 *
	 * @return \chillerlan\Database\Query\Statements\Update
	 */
	public function openBracket($join = null):Update;

	/**
	 * @return \chillerlan\Database\Query\Statements\Update
	 */
	public function closeBracket():Update;

}
