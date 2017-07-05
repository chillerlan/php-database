<?php
/**
 * Trait WhereTrait
 *
 * @filesource   WhereTrait.php
 * @created      12.06.2017
 * @package      chillerlan\Database\Query\Traits
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Dialects;

use chillerlan\Database\Query\Statements\Statement;

trait WhereTrait{

	/**
	 * @var array
	 */
	protected $where = [];

	/**
	 * @param        $val1
	 * @param null   $val2
	 * @param null   $operator
	 * @param bool   $bind
	 * @param string $join
	 *
	 * @return $this
	 */
	protected function _addWhere($val1, $val2 = null, $operator = null, $bind = true, $join = 'AND'){
		$operator = strtoupper(trim($operator));
		$join = strtoupper(trim($join));

		$operators = [
			'=', '>=', '>', '<=', '<', '<>', '!=',
			'|', '&', '<<', '>>', '+', '-', '*', '/', '%', '^', '<=>', '~', '!', 'DIV', 'MOD',
			'IS', 'IS NOT', 'IN', 'NOT IN', 'LIKE', 'NOT LIKE', 'REGEXP', 'NOT REGEXP', 'BETWEEN', 'NOT BETWEEN', 'EXISTS'
		];

		if(in_array($operator, $operators, true)){
			$where = [is_array($val1) ? strtoupper($val1[1]).'('.$this->quote($val1[0]).')' : $this->quote($val1)];
			$values = [];

			if(in_array($operator, ['IN', 'NOT IN'], true)){

				if(is_array($val2)){
					if($bind){
						$where[] = 'IN('.implode(',', array_fill(0, count($val2), '?')).')';
						$values = array_merge($values, $val2);
					}
					else{
						$where[] = 'IN('.implode(',', $val2).')'; // @todo: quote
					}
				}
				else if($val2 instanceof Statement){
					$where[] = 'IN('.$val2->sql().')';
					$values = array_merge($values, $val2->bindValues());
				}

			}
			else if(in_array($operator, ['BETWEEN', 'NOT BETWEEN'], true)){
				// @todo
			}
			else{
				$where[] = $operator;

				if(is_null($val2)){
					$where[] = 'NULL';
				}
				else if(is_bool($val2)){
					$where[] = $val2 ? 'TRUE' : 'FALSE';
				}
				else if(in_array(strtolower($val2), ['null', 'false', 'true', 'unknown'])){
					$where[] = strtoupper($val2);
				}
				else if($val2 instanceof Statement){
					$where[] = '('.$val2->sql().')';
					$values = array_merge($values, $val2->bindValues());
				}
				else{
					if($bind){
						$where[] = '?';
						$values[] = $val2;
					}
					else{
						if(!empty($val2)){
							$where[] = $val2;
						}
					}
				}

			}

			$this->bindValues = array_merge($this->bindValues, $values);
			$this->where[] = [
				'join' => in_array($join, ['AND', 'OR', 'XOR']) ? $join : 'AND',
				'stmt' => implode(' ', $where),
			];

		}

		return $this;
	}

	/**
	 * @param string|null $join
	 *
	 * @return $this
	 */
	protected function _openBracket($join = null){
		$join = strtoupper(trim($join));

		if(in_array($join, ['AND', 'OR', 'XOR'])){
			$this->where[] = $join;
		}

		$this->where[] = '(';

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function _closeBracket(){
		$this->where[] = ')';

		return $this;
	}

	/**
	 * @return string
	 */
	protected function _getWhere():string {
		$where = [];

		foreach($this->where as $k => $v){
			$last = $this->where[$k-1] ?? false;

			if(in_array($v, ['AND', 'OR', 'XOR', '(', ')'], true)){
				$where[] = $v;

				continue;
			}

			if(!is_array($v)){
				continue;
			}

			if(!$last || $last === '('){
				$where[] = $v['stmt'];
			}
			else{
				$where[] = $v['join'].' '.$v['stmt'];
			}

		}

		return !empty($where) ? PHP_EOL.'WHERE '.implode(' '.PHP_EOL."\t", $where) : '';
	}

}
