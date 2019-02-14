<?php
/**
 * Trait WhereTrait
 *
 * @filesource   WhereTrait.php
 * @created      12.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

/**
 * https://xkcd.com/1409/
 *
 * @implements \chillerlan\Database\Query\Where
 *
 * @property \chillerlan\Database\Drivers\DriverInterface $db
 * @property \chillerlan\Database\Dialects\Dialect        $dialect
 * @property array                                        $bindValues
 */
trait WhereTrait{

	private $operators = [
		'=', '>=', '>', '<=', '<', '<>', '!=',
		'|', '&', '<<', '>>', '+', '-', '*', '/',
		'%', '^', '<=>', '~', '!', 'DIV', 'MOD',
		'IS', 'IS NOT', 'IN', 'NOT IN', 'LIKE',
		'NOT LIKE', 'REGEXP', 'NOT REGEXP',
		'EXISTS', 'ANY', 'SOME',
//		'BETWEEN', 'NOT BETWEEN',
	];

	private $joinArgs = ['AND', 'OR', 'XOR'];
	/**
	 * @var array
	 */
	protected $where = [];

	/**
	 * @param mixed       $val1
	 * @param mixed|null  $val2
	 * @param string|null $operator
	 * @param bool|null   $bind
	 * @param string|null $join
	 *
	 * @return $this
	 */
	public function where($val1, $val2 = null, string $operator = null, bool $bind = null, string $join = null){
		$operator = $operator !== null ? strtoupper(trim($operator)) : '=';
		$bind     = $bind ?? true;

		$join = strtoupper(trim($join));
		$join = in_array($join, $this->joinArgs, true) ? $join : 'AND';

		if(in_array($operator, $this->operators, true)){
			$where = [
				is_array($val1)
					? strtoupper($val1[1]).'('.$this->dialect->quote($val1[0]).')'
					: $this->dialect->quote($val1)
			];

			if(in_array($operator, ['IN', 'NOT IN', 'ANY', 'SOME',], true)){

				if(is_array($val2)){

					if($bind){
						$where[] = $operator.'('.implode(',', array_fill(0, count($val2), '?')).')';
						$this->bindValues = array_merge($this->bindValues, $val2);
					}
					else{
						$where[] = $operator.'('.implode(',', array_map([$this->db, 'escape'], $val2)).')'; // @todo: quote
					}

				}
				else if($val2 instanceof Statement){
					$where[] = $operator.'('.$val2->sql().')';
					$this->bindValues = array_merge($this->bindValues, $val2->bindValues());
				}

			}
//			else if(in_array($operator, ['BETWEEN', 'NOT BETWEEN'], true)){
				// @todo
//			}
			else{
				$where[] = $operator;

				if($val2 instanceof Statement){
					$where[] = '('.$val2->sql().')';
					$this->bindValues = array_merge($this->bindValues, $val2->bindValues());
				}
				elseif(is_null($val2)){
					$where[] = 'NULL';
				}
				elseif(is_bool($val2)){
					$where[] = $val2 ? 'TRUE' : 'FALSE';
				}
				elseif(in_array(strtolower($val2), ['null', 'false', 'true', 'unknown'], true)){
					$where[] = strtoupper($val2);
				}
				else {

					if($bind){
						$where[] = '?';
						$this->bindValues[] = $val2;
					}
					else{
						if(!empty($val2)){
							$where[] = $val2;
						}
					}

				}

			}

			$this->where[] = [
				'join' => $join,
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
	public function openBracket(string $join = null){
		$join = strtoupper(trim($join));

		if(in_array($join, $this->joinArgs, true)){
			$this->where[] = $join;
		}

		$this->where[] = '(';

		return $this;
	}

	/**
	 * @return $this
	 */
	public function closeBracket(){
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

			if(in_array($v,  $this->joinArgs + ['(', ')'], true)){
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

		return !empty($where) ? 'WHERE '.implode(' ', $where) : '';
	}

}
