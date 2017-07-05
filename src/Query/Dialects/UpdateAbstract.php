<?php
/**
 * Class UpdateAbstract
 *
 * @filesource   UpdateAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Dialects;

use chillerlan\Database\Query\QueryException;
use chillerlan\Database\Query\Statements\{Statement, Update};

abstract class UpdateAbstract extends StatementAbstract implements Update{
	use WhereTrait;

	/**
	 * @var string
	 */
	protected $table;

	/**
	 * @var array
	 */
	protected $set = [];

	/**
	 * @return string
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function sql():string{

		if(empty($this->table)){
			throw new QueryException('no table specified');
		}

		if(empty($this->set)){
			throw new QueryException('no fields to update specified');
		}

		$sql  = 'UPDATE ';
		$sql .= $this->quote($this->table);
		$sql .= ' SET ';
		$sql .= implode(', ', $this->set);
		$sql .= $this->_getWhere();

		return $sql;
	}

	/**
	 * @param string|null $tablename
	 *
	 * @return \chillerlan\Database\Query\Statements\Update
	 */
	public function table(string $tablename):Update{
		$this->table = $tablename;

		return $this;
	}

	/**
	 * @param array $set
	 * @param bool  $bind
	 *
	 * @return \chillerlan\Database\Query\Statements\Update
	 */
	public function set(array $set, bool $bind = true):Update{

		foreach($set as $k => $v){

			if($v instanceof Statement){
				$this->set[] = $this->quote($k).' = ('.$v->sql().')';
				$this->bindValues = array_merge($this->bindValues, $v->bindValues());
			}
			elseif(is_array($v)){
				// @todo: [expr, bindval1, bindval2, ...]
			}
			else{
				if($bind){
					$this->set[] = $this->quote($k).' = ?';
					$this->bindValues[] = $v;
				}
				else{
					$this->set[] = is_int($k)
						? $this->quote($v).' = ?'
						: $this->quote($k).' = '.$v;
				}
			}
		}

		return $this;
	}

	/**
	 * @param        $val1
	 * @param        $val2
	 * @param string $operator
	 * @param bool   $bind
	 * @param string $join
	 *
	 * @return \chillerlan\Database\Query\Statements\Update
	 */
	public function where($val1, $val2, $operator = '=', $bind = true, $join = 'AND'):Update{
		return $this->_addWhere($val1, $val2, $operator, $bind, $join);
	}

	/**
	 * @param null $join
	 *
	 * @return \chillerlan\Database\Query\Statements\Update
	 */
	public function openBracket($join = null):Update{
		return $this->_openBracket($join); // @codeCoverageIgnore
	}

	/**
	 * @return \chillerlan\Database\Query\Statements\Update
	 */
	public function closeBracket():Update{
		return $this->_closeBracket(); // @codeCoverageIgnore
	}

}
