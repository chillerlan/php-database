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

	public function distinct():Select;
	public function cols(array $expressions):Select;
	public function from(array $expressions):Select;
#	public function join():Select;
	public function groupBy(array $expressions):Select;
#	public function having():Select;
#	public function union():Select;
	public function where($val1, $val2, $operator = '=', $bind = true, $join = 'AND'):Select;
	public function orderBy(array $expressions):Select;
	public function offset(int $offset):Select;
	public function limit(int $limit):Select;
	public function openBracket($join = null):Select;
	public function closeBracket():Select;
	public function count():int;
	public function cached():Select;

}
