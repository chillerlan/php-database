<?php
/**
 * Class DeleteAbstract
 *
 * @filesource   DeleteAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Dialects;

use chillerlan\Database\Query\QueryException;
use chillerlan\Database\Query\Statements\Delete;

abstract class DeleteAbstract extends StatementAbstract implements Delete{
	use WhereTrait;

	/**
	 * @var string
	 */
	protected $table;

	/**
	 * @return string
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function sql():string{

		if(empty($this->table)){
			throw new QueryException('no table specified');
		}


		$sql = 'DELETE ';
		$sql .= 'FROM '.$this->quote($this->table);
		$sql .= $this->_getWhere();

		return $sql;
	}

	/**
	 * @param string $table
	 *
	 * @return \chillerlan\Database\Query\Statements\Delete
	 */
	public function from(string $table):Delete{
		$this->table = trim($table);

		return $this;
	}

	/**
	 * @param        $val1
	 * @param        $val2
	 * @param string $operator
	 * @param bool   $bind
	 * @param string $join
	 *
	 * @return \chillerlan\Database\Query\Statements\Delete
	 */
	public function where($val1, $val2, $operator = '=', $bind = true, $join = 'AND'):Delete{
		return $this->_addWhere($val1, $val2, $operator, $bind, $join);
	}

	/**
	 * @param null $join
	 *
	 * @return \chillerlan\Database\Query\Statements\Delete
	 */
	public function openBracket($join = null):Delete{
		return $this->_openBracket($join); // @codeCoverageIgnore
	}

	/**
	 * @return \chillerlan\Database\Query\Statements\Delete
	 */
	public function closeBracket():Delete{
		return $this->_closeBracket(); // @codeCoverageIgnore
	}


}
