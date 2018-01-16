<?php
/**
 * Class DialectAbstract
 *
 * @filesource   DialectAbstract.php
 * @created      11.01.2018
 * @package      chillerlan\Database\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Dialects;

use chillerlan\Database\Drivers\DriverInterface;
use chillerlan\Database\Query\QueryException;

/**
 * please don't look at it
 *
 * @link https://en.wikibooks.org/wiki/SQL_Dialects_Reference
 * @link https://en.wikibooks.org/wiki/Converting_MySQL_to_PostgreSQL
 */
abstract class DialectAbstract implements Dialect{

	/**
	 * @var string[]
	 */
	protected $quotes;

	/**
	 * @var string
	 */
	protected $charset = 'utf8';

	/**
	 * @var \chillerlan\Database\Drivers\DriverInterface
	 */
	protected $db;

	/**
	 * Dialect constructor.
	 *
	 * @param \chillerlan\Database\Drivers\DriverInterface $driver
	 */
	public function __construct(DriverInterface $driver){
		$this->db = $driver;
	}

	/** @inheritdoc */
	public function select(array $cols, array $from, string $where = null, $limit = null, $offset = null, bool $distinct = null, array $groupby, array $orderby):array{
		$sql = ['SELECT'];

		if($distinct){
			$sql[] = 'DISTINCT';
		}

		!empty($cols)
			? $sql[] = implode(', ', $cols)
			: $sql[] = '*';

		$sql[] = 'FROM';
		$sql[] = implode(', ', $from);
		$sql[] = $where;

		if(!empty($groupby)){
			$sql[] = 'GROUP BY';
			$sql[] = implode(', ', $groupby);
		}

		if(!empty($orderby)){
			$sql[] = 'ORDER BY';
			$sql[] = implode(', ', $orderby);
		}

		if($limit !== null){
			$sql[] = 'LIMIT';
			$sql[] = $offset !== null ? '?, ?' : '?';
		}

		return $sql;
	}

	/** @inheritdoc */
	public function insert(string $table, array $fields, string $onConflict = null):array{
		$sql = ['INSERT INTO'];
		$sql[] = $this->quote($table);
		$sql[] = '('.$this->quotes[0].implode($this->quotes[1].', '.$this->quotes[0], $fields).$this->quotes[1].')';
		$sql[] = 'VALUES';
		$sql[] = '('.implode(', ', array_fill(0, count($fields), '?')).')';

		return $sql;
	}

	/** @inheritdoc */
	public function update(string $table, array $set, string $where):array{
		$sql = ['UPDATE'];
		$sql[] = $this->quote($table);
		$sql[] = 'SET';
		$sql[] = implode(', ', $set);
		$sql[] = $where;

		return $sql;
	}

	/** @inheritdoc */
	public function delete(string $table, string $where):array{
		$sql = ['DELETE FROM'];
		$sql[] = $this->quote($table);
		$sql[] = $where;

		return $sql;
	}

	/** @inheritdoc */
	public function createDatabase(string $dbname, bool $ifNotExists = null, string $collate = null):array{
		throw new QueryException('not supported');
	}

	/** @inheritdoc */
	public function quote(string $str):string{ // @todo: fixme
		return $this->quotes[0].implode($this->quotes[1].'.'.$this->quotes[0], explode('.', $str)).$this->quotes[1];
	}

	/** @inheritdoc */
	public function dropDatabase(string $dbname, bool $ifExists){
		$sql = ['DROP DATABASE'];

		if($ifExists){
			$sql[] = 'IF EXISTS';
		}

		$sql[] = $this->quote($dbname);

		return $sql;
	}

	/** @inheritdoc */
	public function dropTable(string $table, bool $ifExists):array{
		$sql = ['DROP TABLE'];

		if($ifExists){
			$sql[] = 'IF EXISTS';
		}

		$sql[] = $this->quote($table);

		return $sql;
	}

	public function selectCount(array $from, string $where = null, bool $distinct = null, array $groupby = null){
		$sql = ['SELECT'];

		if($distinct){
			$sql[] = 'DISTINCT';
		}

		$sql[] = 'COUNT(*) AS';
		$sql[] = $this->quote('count');
		$sql[] = 'FROM '.implode(', ', $from);
		$sql[] = $where;

		if(!empty($groupby)){
			$sql[] = 'GROUP BY ';
			$sql[] = implode(', ', $groupby);
		}

		return $sql;
	}

	public function orderby(array $expressions):array {
		$orderby = [];

		foreach($expressions as $alias => $expression){

			if(is_string($alias)){

				if(is_array($expression)){
					$dir = strtoupper($expression[0]);

					if(in_array($dir, ['ASC', 'DESC'], true)){
						$orderby[] = isset($expression[1]) ? strtoupper($expression[1]).'('.$this->quote($alias).') '.$dir : $dir;
					}

				}
				else{
					$dir = strtoupper($expression);

					if(in_array($dir, ['ASC', 'DESC'], true)){
						$orderby[] = $this->quote($alias).' '.$dir;
					}

				}

			}
			else{
				$orderby[] = $this->quote($expression);
			}

		}

		return $orderby;
	}

	/** @inheritdoc */
	public function cols(array $expressions):array {

		$_col = function ($expr1, $expr2 = null, $func = null):string {
			switch(true){
				case  $expr2 && $func:
					$col = sprintf('%s(%s) AS %s', strtoupper($func), $expr1, $this->quote($expr2));
					break;
				case  $expr2 && !$func:
					$col = sprintf('%s AS %s', $this->quote($expr1), $this->quote($expr2));
					break;
				case !$expr2 && $func:
					$col = sprintf('%s(%s)', strtoupper($func), $expr1);
					break;
				case !$expr2 && !$func:
				default:
					$col = $this->quote($expr1);
			}

			return $col;
		};

		$r = [];

		foreach($expressions as $k => $ref){
			if(is_string($k)){
				$r[$ref[0] ?? $k] = is_array($ref)
					? $_col($ref[0], $k, $ref[1] ?? null)
					: $_col($ref, $k);
			}
			else{
				$r[$ref] = is_array($ref)
					? $_col($ref[0], null, $ref[1] ?? null)
					: $_col($ref);
			}

		}

		return $r;
	}


	/** @inheritdoc */
	public function from(array $expressions):array {

		$_from = function (string $table, string $ref = null):string {
			// @todo: quotes
			$from = $this->quote($table);

			if($ref){
				$from = sprintf('%s AS %s', $this->quote($ref), $this->quote($table));// @todo: index hint
			}

			return $from;
		};

		$r = [];

		foreach($expressions as $k => $ref){

			if(is_string($k)){
				$r[$ref ?? $k] = $_from($k, $ref);
			}
			else{
				$x = explode(' ', $ref);

				if(count($x) === 2){
					$r[$ref ?? $k] = $_from($x[0], $x[1]);
				}
				else{
					$r[$ref] = $_from($ref);
				}
			}

		}

		return $r;
	}

	/** @inheritdoc */
	public function enum(string $name, array $values, $defaultValue = null, bool $isNull = null):string {

		$field = $this->quote($name);
		$field .= 'ENUM (\''.implode('\', \'', $values).'\')';

		if(is_bool($isNull)){
			$field .= $isNull ? 'NULL' : 'NOT NULL';
		}

		if(in_array($defaultValue, $values, true)){
			$field .= 'DEFAULT '.(is_int($defaultValue) || is_float($defaultValue) ? $defaultValue : '\''.$defaultValue.'\'');
		}
		elseif($isNull && strtolower($defaultValue) === 'null'){
			$field .= 'DEFAULT NULL';
		}

		return $field;
	}

	/** @inheritdoc */
	public function truncate(string $table):array{
		$sql = ['TRUNCATE TABLE'];
		$sql[] = $this->quote($table);

		return $sql;
	}

	/** @inheritdoc */
	public function showDatabases():array{
		throw new QueryException('not supported');
	}

	/** @inheritdoc */
	public function showTables(string $database = null, string $pattern = null, string $where = null):array{
		throw new QueryException('not supported');
	}

	/** @inheritdoc */
	public function showCreateTable(string $table):array{
		throw new QueryException('not supported');
	}

}
