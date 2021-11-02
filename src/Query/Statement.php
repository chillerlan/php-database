<?php
/**
 * Class Statement
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use chillerlan\Database\Dialects\{Dialect, Firebird, MSSQL};
use chillerlan\Database\Drivers\DriverInterface;
use Psr\Log\{LoggerAwareInterface, LoggerAwareTrait, LoggerInterface};

use function array_fill, array_map, array_merge, count, implode, in_array, is_array,
	is_bool, is_null, max, strtolower, strtoupper, trim;

/**
 * https://xkcd.com/1409/
 */
abstract class Statement implements LoggerAwareInterface{
	use LoggerAwareTrait;

	protected DriverInterface $db;
	protected Dialect $dialect;
	protected ?string $name = null;
	protected ?string $on_conflict = null;
	protected ?string $conflict_target = null;
	protected ?string $charset = null;
	protected bool $ifExists = false;
	protected bool $ifNotExists = false;
	protected array $where = [];
	protected array $sql = [];
	protected bool $multi = false;
	protected bool $cached = false;
	protected int $ttl = 300;
	protected ?int $limit = null;
	protected ?int $offset = null;
	protected array $bindValues = [];

	private array $joinArgs = ['AND', 'OR', 'XOR'];
	private array $operators = [
		'=', '>=', '>', '<=', '<', '<>', '!=',
		'|', '&', '<<', '>>', '+', '-', '*', '/',
		'%', '^', '<=>', '~', '!', 'DIV', 'MOD',
		'IS', 'IS NOT', 'IN', 'NOT IN', 'LIKE',
		'NOT LIKE', 'REGEXP', 'NOT REGEXP',
		'EXISTS', 'ANY', 'SOME',
#		'BETWEEN', 'NOT BETWEEN',
	];


	public function __construct(DriverInterface $db, Dialect $dialect, LoggerInterface $logger = null){
		$this->db      = $db;
		$this->dialect = $dialect;
		$this->logger  = $logger;
	}

	/** */
	protected function setName(string $name):Statement{
		$this->name = trim($name);

		if(empty($this->name)){
			throw new QueryException('no name specified');
		}

		return $this;
	}

	/** */
	protected function setOnConflict(string $name, string $on_conflict = null, string $conflict_target = null):Statement{
		$this->name      = trim($name);
		$on_conflict     = trim(strtoupper($on_conflict));
		$conflict_target = trim($conflict_target);

		if(empty($this->name)){
			throw new QueryException('no name specified');
		}

		if(!empty($on_conflict)){
			$this->on_conflict = $on_conflict;
		}

		if(!empty($conflict_target)){
			$this->conflict_target = $conflict_target;
		}

		return $this;
	}

	/** */
	protected function setCharset(string $charset):Statement{
		$this->charset = trim($charset);

		return $this;
	}

	/** */
	protected function setIfExists():Statement{
		$this->ifExists = true;

		return $this;
	}

	/** */
	protected function setIfNotExists():Statement{
		$this->ifNotExists = true;

		return $this;
	}

	/**
	 * @param mixed       $val1
	 * @param mixed|null  $val2
	 * @param string|null $operator
	 * @param bool|null   $bind
	 * @param string|null $join
	 *
	 * @return $this
	 */
	protected function setWhere($val1, $val2 = null, string $operator = null, bool $bind = null, string $join = null):Statement{
		$operator = $operator !== null ? strtoupper(trim($operator)) : '=';
		$bind     ??= true;

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

					if($val2 instanceof BindValues){
						$this->bindValues = array_merge($this->bindValues, $val2->getBindValues());
					}
				}

			}
			// @todo
#			else if(in_array($operator, ['BETWEEN', 'NOT BETWEEN'], true)){}
			else{
				$where[] = $operator;

				if($val2 instanceof Statement){
					$where[] = '('.$val2->sql().')';

					if($val2 instanceof BindValues){
						$this->bindValues = array_merge($this->bindValues, $val2->getBindValues());
					}
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

	/** */
	protected function setOpenBracket(string $join = null):Statement{
		$join = strtoupper(trim($join));

		if(in_array($join, $this->joinArgs, true)){
			$this->where[] = $join;
		}

		$this->where[] = '(';

		return $this;
	}

	/** */
	protected function setCloseBracket():Statement{
		$this->where[] = ')';

		return $this;
	}

	/** */
	protected function _getWhere():string{
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

	/**
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	protected function getSQL():array{
		throw new QueryException('getSQL() not supported/implemented');
	}

	/**
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function sql(bool $multi = null):string{

		if(!$this instanceof Query){
			throw new QueryException('Query not supported');
		}

		$this->multi = $multi ?? false;

		$sql = trim(implode(' ', $this->getSQL()));

		// this should only happen on a corrupt dialect implementation
		if(empty($sql)){
			throw new QueryException('empty sql'); // @codeCoverageIgnore
		}

		return $sql;
	}

	/**
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function getBindValues():array{

		if(!$this instanceof BindValues){
			throw new QueryException('BindValues not supported');
		}

		// @todo: cleanup/simplify
		if($this->dialect instanceof Firebird){

			if($this->limit !== null){
				$this->bindValues = array_merge([
					'limit'  => $this->limit,
					'offset' => $this->offset ?? 0,
				], $this->bindValues);
			}

		}
		elseif($this->dialect instanceof MSSQL){

			if($this->limit !== null){
				$this->bindValues = array_merge([
					'offset' => $this->offset ?? 0,
					'limit'  => $this->limit,
				], $this->bindValues);
			}

		}
		else{

			if($this->offset !== null){
				$this->bindValues['offset'] = $this->offset;
			}

			if($this->limit !== null){
				$this->bindValues['limit'] = $this->limit;
			}

		}

		return $this->bindValues;
	}

	/** */
	protected function addBindValue(string $key, $value):void{
		$this->bindValues[$key] = $value;
	}

	/** */
	protected function setLimit(int $limit):Statement{
		$this->limit = max($limit, 0);

		return $this;
	}

	/** */
	protected function setOffset(int $offset):Statement{
		$this->offset = max($offset, 0);

		return $this;
	}

	/** */
	protected function setCached(int $ttl = null):Statement{
		$this->cached = true;

		if($ttl > 0){
			$this->ttl = $ttl;
		}

		return $this;
	}

	/**
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function query(string $index = null){

		if(!$this instanceof Query){
			throw new QueryException('Query not supported');
		}

		$sql    = $this->sql(false);
		$values = $this instanceof BindValues ? $this->getBindValues() : null;

		$this->logger->debug('QueryTrait::query()', ['method' => __METHOD__, 'sql' => $sql, 'val' => $values, 'index' => $index]);

		if($this->cached && $this instanceof Select){
			return $this->db->preparedCached($sql, $values, $index, true, $this->ttl);
		}

		return $this->db->prepared($sql, $values, $index);
	}

	/**
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function multi(array $values = null):bool{

		if(!$this instanceof MultiQuery){
			throw new QueryException('MultiQuery not supported');
		}

		// @todo: fix values/bindvalues
		$sql    = $this->sql(true);
		$values = $values ?? ($this instanceof BindValues ? $this->getBindValues() : []);

		$this->logger->debug('MultiQueryTrait::multi()', ['method' => __METHOD__, 'sql' => $sql, 'val' => $values]);

		return $this->db->multi($sql, $values);
	}

	/**
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function callback(array $values, callable $callback):bool{

		if(!$this instanceof MultiQuery){
			throw new QueryException('MultiQuery not supported');
		}

		$sql = $this->sql(true);

		$this->logger->debug('MultiQueryTrait::callback()', ['method' => __METHOD__, 'sql' => $sql, 'val' => $values]);

		return $this->db->multiCallback($sql, $values, $callback);
	}

}
