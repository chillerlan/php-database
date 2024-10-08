<?php
/**
 * Class Select
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

use function implode;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/select.html
 * @link https://www.postgresql.org/docs/current/static/sql-select.html
 * @link https://docs.microsoft.com/de-de/sql/t-sql/queries/select-clause-transact-sql
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-dml-select.html
 * @link https://www.sqlite.org/lang_select.html
 */
class Select extends StatementAbstract implements Where, Limit, BindValues, CachedQuery{

	protected bool  $distinct = false;
	protected array $cols     = [];
	protected array $from     = [];
	protected array $orderby  = [];
	protected array $groupby  = [];

	public function where(mixed $val1, mixed $val2 = null, string|null $operator = null, bool|null $bind = null, string|null $join = null):static{
		return $this->setWhere($val1, $val2, $operator, $bind, $join);
	}

	public function openBracket(string|null $join = null):static{
		return $this->setOpenBracket($join);
	}

	public function closeBracket():static{
		return $this->setCloseBracket();
	}

	public function limit(int $limit):static{
		return $this->setLimit($limit);
	}

	public function offset(int $offset):static{
		return $this->setOffset($offset);
	}

	public function cached(int|null $ttl = null):static{
		return $this->setCached($ttl);
	}

	protected function sql():array{

		if(empty($this->from)){
			throw new QueryException('no FROM expression specified');
		}

		return $this->dialect->select($this->cols, $this->from, $this->getWhere(), $this->limit, $this->offset, $this->distinct, $this->groupby, $this->orderby);
	}

	public function distinct():static{
		$this->distinct = true;

		return $this;
	}

	public function cols(array $expressions):static{
		$this->cols = $this->dialect->cols($expressions);

		return $this;
	}

	public function from(array $expressions):static{
		$this->from = $this->dialect->from($expressions);

		return $this;
	}

	public function orderBy(array $expressions):static{
		$this->orderby = $this->dialect->orderby($expressions);

		return $this;
	}

	public function groupBy(array $expressions):static{

		foreach($expressions as $expression){
			$this->groupby[] = $this->dialect->quote($expression);
		}

		return $this;
	}

	public function count():int{

		if(empty($this->from)){
			throw new QueryException('no FROM expression specified');
		}

		$sql    = $this->dialect->selectCount($this->from, $this->getWhere(), $this->distinct, $this->groupby);
		$result = $this->db->prepared(implode(' ', $sql), $this->bindValues);

		if($result->count() > 0){
			return (int)$result[0]->count;
		}

		return -1;
	}

}
