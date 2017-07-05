<?php
/**
 * Class SelectAbstract
 *
 * @filesource   SelectAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Dialects;


use chillerlan\Database\Query\QueryException;
use chillerlan\Database\Query\Statements\Select;
use chillerlan\Database\Result;

abstract class SelectAbstract extends StatementAbstract implements Select{
	use WhereTrait;

	/**
	 * @var bool
	 */
	protected $distinct = false;

	/**
	 * @var array
	 */
	protected $cols = [];

	/**
	 * @var array
	 */
	protected $from = [];

	/**
	 * @var array
	 */
	protected $orderby = [];

	/**
	 * @var array
	 */
	protected $groupby = [];

	/**
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function cached():Select{
		$this->cached = true;

		return $this;
	}

	/**
	 * @param        $val1
	 * @param        $val2
	 * @param string $operator
	 * @param bool   $bind
	 * @param string $join
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function where($val1, $val2, $operator = '=', $bind = true, $join = 'AND'):Select{
		return $this->_addWhere($val1, $val2, $operator, $bind, $join);
	}

	/**
	 * @param null $join
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function openBracket($join = null):Select{
		return $this->_openBracket($join); // @codeCoverageIgnore
	}

	/**
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function closeBracket():Select{
		return $this->_closeBracket(); // @codeCoverageIgnore
	}

	/**
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function distinct():Select{
		$this->distinct = true;

		return $this;
	}

	/**
	 * @param      $expr1
	 * @param null $expr2
	 * @param null $func
	 *
	 * @return void
	 */
	protected function addColumn($expr1, $expr2 = null, $func = null){
		// @todo: quotes
		switch(true){
			case  $expr2 && $func:
				$col = sprintf('%s(%s) AS %s', strtoupper($func), $this->quote($expr1), $this->quote($expr2)); break;
			case  $expr2 && !$func:
				$col = sprintf('%s AS %s', $this->quote($expr1), $this->quote($expr2)); break;
			case !$expr2 && $func:
				$col = sprintf('%s(%s)', strtoupper($func), $this->quote($expr1)); break;
			case !$expr2 && !$func:
			default:
				$col = $this->quote($expr1);
		}

		$this->cols[$expr2 ?? $expr1] = $col;
	}

	/**
	 * @param array $expressions
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function cols(array $expressions):Select{

		foreach($expressions as $k => $ref){

			if(is_string($k)){
				is_array($ref)
					? $this->addColumn($ref[0], $k, $ref[1] ?? null)
					: $this->addColumn($ref ,$k);
			}
			else{
				is_array($ref)
					? $this->addColumn($ref[0], null, $ref[1] ?? null)
					: $this->addColumn($ref);
			}

		}

		return $this;
	}

	/**
	 * @param string      $table
	 * @param string|null $ref
	 */
	protected function _addFrom(string $table, string $ref = null){
		// @todo: quotes
		$from = $this->quote($table);

		if($ref){
			$from = sprintf('%s AS %s', $this->quote($ref), $this->quote($table));// @todo: index hint
		}

		$this->from[$ref ?? $table] = $from;
	}

	/**
	 * @param array $expressions
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function from(array $expressions):Select{

		foreach($expressions as $k => $ref){

			if(is_string($k)){
				$this->_addFrom($k, $ref);
			}
			else{
				$x = explode(' ', $ref);

				if(count($x) === 2){
					$this->_addFrom($x[0], $x[1]);
				}
				else{
					$this->_addFrom($ref);
				}
			}

		}

		return $this;
	}

	/**
	 * @param int $limit
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function limit(int $limit):Select{
		$this->limit = $limit;

		return $this;
	}

	/**
	 * @param int $offset
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function offset(int $offset):Select{
		$this->offset = $offset;

		return $this;
	}

	/**
	 * @param array $expressions
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function orderBy(array $expressions):Select{

		foreach($expressions as $alias => $expression){

			if(is_string($alias)){

				if(is_array($expression)){
					$dir = strtoupper($expression[0]);

					if(in_array($dir, ['ASC', 'DESC'])){
						$this->orderby[] =  isset($expression[1]) ? strtoupper($expression[1]).'('.$this->quote($alias).') '.$dir : $dir;
					}

				}
				else{
					$dir = strtoupper($expression);

					if(in_array($dir, ['ASC', 'DESC'])){
						$this->orderby[] =  $this->quote($alias).' '.$dir;
					}

				}

			}
			else{
				$this->orderby[] = $this->quote($expression);
			}

		}

		return $this;
	}

	/**
	 * @param array $expressions
	 *
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function groupBy(array $expressions):Select{

		foreach($expressions as $expression){
			$this->groupby[] = $this->quote($expression);
		}

		return $this;
	}

	/**
	 * @todo
	 *
	 * @return int
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function count():int{

		if(empty($this->from)){
			throw new QueryException('no FROM expression specified');
		}

		$glue = ','.PHP_EOL."\t";

		$sql  = 'SELECT ';
		$sql .= $this->distinct ? 'DISTINCT ' : '';
		$sql .= 'COUNT(*) AS  '.$this->quote('count');
		$sql .= 'FROM '.implode($glue , $this->from);
		$sql .= $this->_getWhere();
		$sql .= !empty($this->groupby) ? PHP_EOL.'GROUP BY '.implode($glue, $this->groupby) : '';

		$q = $this->db->prepared($sql, $this->bindValues);

		if($q instanceof Result && $q->length > 0){
			return (int)$q[0]->count;
		}

		return -1;
	}

}
