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

use chillerlan\Database\Query\QueryException;
use function array_fill, count, explode, implode, in_array, is_array, is_float,
	is_int, is_string, sprintf, strtolower, strtoupper;

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
	protected array $quotes = ['"', '"'];

	/**
	 * @var string
	 */
	protected string $charset = 'utf8';

	public function select(
		array       $cols,
		array       $from,
		string|null $where = null,
		mixed       $limit = null,
		mixed       $offset = null,
		bool|null   $distinct = null,
		array|null  $groupby = null,
		array|null  $orderby = null,
	):array{
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

	public function insert(
		string      $table,
		array       $fields,
		string|null $onConflict = null,
		string|null $conflictTarget = null,
	):array{
		$sql = ['INSERT INTO'];
		$sql[] = $this->quote($table);
		$sql[] = '('.$this->quotes[0].implode($this->quotes[1].', '.$this->quotes[0], $fields).$this->quotes[1].')';
		$sql[] = 'VALUES';
		$sql[] = '('.implode(',', array_fill(0, count($fields), '?')).')';

		return $sql;
	}

	public function update(string $table, array $set, string $where):array{
		$sql = ['UPDATE'];
		$sql[] = $this->quote($table);
		$sql[] = 'SET';
		$sql[] = implode(', ', $set);
		$sql[] = $where;

		return $sql;
	}

	public function delete(string $table, string $where):array{
		$sql = ['DELETE FROM'];
		$sql[] = $this->quote($table);
		$sql[] = $where;

		return $sql;
	}

	public function createDatabase(string $dbname, bool|null $ifNotExists = null, string|null $collate = null):array{
		throw new QueryException('not supported');
	}

	public function quote(string $str):string{ // @todo: fixme
		return $this->quotes[0].implode($this->quotes[1].'.'.$this->quotes[0], explode('.', $str)).$this->quotes[1];
	}

	public function dropDatabase(string $dbname, bool $ifExists):array{
		$sql = ['DROP DATABASE'];

		if($ifExists){
			$sql[] = 'IF EXISTS';
		}

		$sql[] = $this->quote($dbname);

		return $sql;
	}

	public function dropTable(string $table, bool $ifExists):array{
		$sql = ['DROP TABLE'];

		if($ifExists){
			$sql[] = 'IF EXISTS';
		}

		$sql[] = $this->quote($table);

		return $sql;
	}

	public function selectCount(
		array       $from,
		string|null $where = null,
		bool|null   $distinct = null,
		array|null  $groupby = null,
	):array{
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

	public function cols(array $expressions):array {
		$r = [];

		foreach($expressions as $k => $ref){
			if(is_string($k)){
				$r[$ref[0] ?? $k] = is_array($ref)
					? $this->col_ref($ref[0], $k, $ref[1] ?? null)
					: $this->col_ref($ref, $k);
			}
			else{
				$r[$ref] = is_array($ref)
					? $this->col_ref($ref[0], null, $ref[1] ?? null)
					: $this->col_ref($ref);
			}

		}

		return $r;
	}

	protected function col_ref(string $expr1, string|null $expr2 = null, string|null $func = null):string{
		// @todo: fixme, cleanup
		// errors on [alias => col, alias, alias => col...]
		return match(true){
			$expr2 !== null && $func !== null => sprintf('%s(%s) AS %s', strtoupper($func), $expr1, $this->quote($expr2)),
			$expr2 !== null && $func === null => sprintf('%s AS %s', $this->quote($expr1), $this->quote($expr2)),
			$expr2 === null && $func !== null => sprintf('%s(%s)', strtoupper($func), $expr1),
			default                           => $this->quote($expr1),
		};
	}


	public function from(array $expressions):array {
		$r = [];

		foreach($expressions as $k => $ref){

			if(is_string($k)){
				$r[$ref ?? $k] = $this->from_ref($k, $ref);
			}
			else{
				$x = explode(' ', $ref);

				if(count($x) === 2){
					$r[$ref ?? $k] = $this->from_ref($x[0], $x[1]);
				}
				else{
					$r[$ref] = $this->from_ref($ref);
				}
			}

		}

		return $r;
	}

	protected function from_ref(string $table, string|null $ref = null):string{
		// @todo: quotes
		$from = $this->quote($table);

		if($ref !== null){
			$from = sprintf('%s AS %s', $this->quote($ref), $this->quote($table)); // @todo: index hint
		}

		return $from;
	}

	public function enum(string $name, array $values, mixed $defaultValue = null, bool|null $isNull = null):string{
		$field = $this->quote($name);
		$field .= 'ENUM (\''.implode('\', \'', $values).'\')';

		if($isNull !== null){
			$field .= $isNull === true ? 'NULL' : 'NOT NULL';
		}

		if(in_array($defaultValue, $values, true)){
			$field .= 'DEFAULT '.(is_int($defaultValue) || is_float($defaultValue) ? $defaultValue : '\''.$defaultValue.'\'');
		}
		elseif($isNull && strtolower($defaultValue) === 'null'){
			$field .= 'DEFAULT NULL';
		}

		return $field;
	}

	public function truncate(string $table):array{
		$sql = ['TRUNCATE TABLE'];
		$sql[] = $this->quote($table);

		return $sql;
	}

	public function showDatabases():array{
		throw new QueryException('not supported');
	}

	public function showTables(string|null $database = null, string|null $pattern = null, string|null $where = null):array{
		throw new QueryException('not supported');
	}

	public function showCreateTable(string $table):array{
		throw new QueryException('not supported');
	}

}
