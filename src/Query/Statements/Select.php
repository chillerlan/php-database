<?php
/**
 * Interface Select
 *
 * @filesource   Select.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

interface Select extends Statement{

#	public function join():Select;
#	public function having():Select;
#	public function union():Select;

	/**
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function distinct():Select;

	/**
	 * @param array $expressions
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function cols(array $expressions):Select;

	/**
	 * @param array $expressions
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function from(array $expressions):Select;

	/**
	 * @param array $expressions
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function groupBy(array $expressions):Select;

	/**
	 * @param             $val1
	 * @param             $val2
	 * @param null|string $operator
	 * @param bool|null   $bind
	 * @param null|string $join
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function where($val1, $val2, string $operator = null, bool $bind = null, string $join = null):Select;

	/**
	 * @param array $expressions
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function orderBy(array $expressions):Select;

	/**
	 * @param int $offset
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function offset(int $offset):Select;

	/**
	 * @param int $limit
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function limit(int $limit):Select;

	/**
	 * @param null|string $join
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function openBracket(string $join = null):Select;

	/**
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function closeBracket():Select;

	/**
	 * @return int
	 */
	public function count():int;

	/**
	 * @param int|null $ttl
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function cached(int $ttl = null):Select;

}
