<?php
/**
 * Class UpdateAbstract
 *
 * @filesource   UpdateAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

abstract class UpdateAbstract extends StatementAbstract implements Update{
	use WhereTrait, NameTrait;

	/**
	 * @var array
	 */
	protected $set = [];

	/**
	 * @return string
	 * @throws \chillerlan\Database\Query\Statements\StatementException
	 */
	public function sql():string{

		if(empty($this->set)){
			throw new StatementException('no fields to update specified');
		}

		$sql  = 'UPDATE ';
		$sql .= $this->quote($this->name);
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
		return $this->_name($tablename);
	}

	/**
	 * @param array $set
	 * @param bool  $bind
	 *
	 * @return \chillerlan\Database\Query\Statements\Update
	 */
	public function set(array $set, bool $bind = null):Update{
		$bind = $bind !== null ? $bind : true;

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
	public function where($val1, $val2, string $operator = null, bool $bind = null, string $join = null):Update{
		return $this->_addWhere($val1, $val2, $operator, $bind, $join);
	}

	/**
	 * @param string $join
	 *
	 * @return \chillerlan\Database\Query\Statements\Update
	 */
	public function openBracket(string $join = null):Update{
		return $this->_openBracket($join); // @codeCoverageIgnore
	}

	/**
	 * @return \chillerlan\Database\Query\Statements\Update
	 */
	public function closeBracket():Update{
		return $this->_closeBracket(); // @codeCoverageIgnore
	}

}
