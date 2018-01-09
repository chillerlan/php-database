<?php
/**
 * Class SelectAbstract
 *
 * @filesource   SelectAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Select
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Select;

use chillerlan\Database\Query\{
	StatementAbstract, StatementException, WhereTrait
};
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
	 * @todo: TTL
	 *
	 * @inheritdoc
	 */
	public function cached(int $ttl = null):Select{
		$this->cached = true;

		return $this;
	}

	/** @inheritdoc */
	public function distinct():Select{
		$this->distinct = true;

		return $this;
	}

	/** @inheritdoc */
	public function cols(array $expressions):Select{

		foreach($expressions as $k => $ref){

			if(is_string($k)){
				is_array($ref)
					? $this->_addColumn($ref[0], $k, $ref[1] ?? null)
					: $this->_addColumn($ref ,$k);
			}
			else{
				is_array($ref)
					? $this->_addColumn($ref[0], null, $ref[1] ?? null)
					: $this->_addColumn($ref);
			}

		}

		return $this;
	}

	/** @inheritdoc */
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

	/** @inheritdoc */
	public function limit(int $limit):Select{
		$this->limit = $limit >= 0 ? $limit : 0;

		return $this;
	}

	/** @inheritdoc */
	public function offset(int $offset):Select{
		$this->offset = $offset >= 0 ? $offset : 0;

		return $this;
	}

	/** @inheritdoc */
	public function orderBy(array $expressions):Select{

		foreach($expressions as $alias => $expression){

			if(is_string($alias)){

				if(is_array($expression)){
					$dir = strtoupper($expression[0]);

					if(in_array($dir, ['ASC', 'DESC'], true)){
						$this->orderby[] =  isset($expression[1]) ? strtoupper($expression[1]).'('.$this->quote($alias).') '.$dir : $dir;
					}

				}
				else{
					$dir = strtoupper($expression);

					if(in_array($dir, ['ASC', 'DESC'], true)){
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

	/** @inheritdoc */
	public function groupBy(array $expressions):Select{

		foreach($expressions as $expression){
			$this->groupby[] = $this->quote($expression);
		}

		return $this;
	}

	/** @inheritdoc */
	public function count():int{

		if(empty($this->from)){
			throw new StatementException('no FROM expression specified');
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

	/**
	 * @todo: quotes
	 *
	 * @param      $expr1
	 * @param null $expr2
	 * @param null $func
	 *
	 * @return void
	 */
	protected function _addColumn($expr1, $expr2 = null, $func = null):void{
		switch(true){
			case  $expr2 && $func:
				$col = sprintf('%s(%s) AS %s', strtoupper($func), $expr1, $this->quote($expr2)); break;
			case  $expr2 && !$func:
				$col = sprintf('%s AS %s', $this->quote($expr1), $this->quote($expr2)); break;
			case !$expr2 && $func:
				$col = sprintf('%s(%s)', strtoupper($func), $expr1); break;
			case !$expr2 && !$func:
			default:
				$col = $this->quote($expr1);
		}

		$this->cols[$expr2 ?? $expr1] = $col;
	}

	/**
	 * @param string      $table
	 * @param string|null $ref
	 *
	 * @return void
	 */
	protected function _addFrom(string $table, string $ref = null):void{
		// @todo: quotes
		$from = $this->quote($table);

		if($ref){
			$from = sprintf('%s AS %s', $this->quote($ref), $this->quote($table));// @todo: index hint
		}

		$this->from[$ref ?? $table] = $from;
	}

}
