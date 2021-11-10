<?php
/**
 * Class Select
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use chillerlan\Database\ResultInterface;
use function implode;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/select.html
 * @link https://www.postgresql.org/docs/current/static/sql-select.html
 * @link https://docs.microsoft.com/de-de/sql/t-sql/queries/select-clause-transact-sql
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-dml-select.html
 * @link https://www.sqlite.org/lang_select.html
 */
class Select extends Statement implements Where, Limit, BindValues, Query, CachedQuery{

	protected bool $distinct = false;
	protected array $cols = [];
	protected array $from = [];
	protected array $orderby = [];
	protected array $groupby = [];

	public function where($val1, $val2 = null, string $operator = null, bool $bind = null, string $join = null):Select{
		return $this->setWhere($val1, $val2, $operator, $bind, $join);
	}

	public function openBracket(string $join = null):Select{
		return $this->setOpenBracket($join);
	}

	public function closeBracket():Select{
		return $this->setCloseBracket();
	}

	public function limit(int $limit):Select{
		return $this->setLimit($limit);
	}

	public function offset(int $offset):Select{
		return $this->setOffset($offset);
	}

	public function cached(int $ttl = null):Select{
		return $this->setCached($ttl);
	}

	/** @inheritdoc */
	protected function getSQL():array{

		if(empty($this->from)){
			throw new QueryException('no FROM expression specified');
		}

		return $this->dialect->select($this->cols, $this->from, $this->_getWhere(), $this->limit, $this->offset, $this->distinct, $this->groupby, $this->orderby);
	}

	public function distinct():Select{
		$this->distinct = true;

		return $this;
	}

	public function cols(array $expressions):Select{
		$this->cols = $this->dialect->cols($expressions);

		return $this;
	}

	public function from(array $expressions):Select{
		$this->from = $this->dialect->from($expressions);

		return $this;
	}

	public function orderBy(array $expressions):Select{
		$this->orderby = $this->dialect->orderby($expressions);

		return $this;
	}

	public function groupBy(array $expressions):Select{

		foreach($expressions as $expression){
			$this->groupby[] = $this->dialect->quote($expression);
		}

		return $this;
	}

	public function count():int{

		if(empty($this->from)){
			throw new QueryException('no FROM expression specified');
		}

		$sql    = $this->dialect->selectCount($this->from, $this->_getWhere(), $this->distinct, $this->groupby);
		$result = $this->db->prepared(implode(' ', $sql), $this->bindValues);

		if($result instanceof ResultInterface && $result->count() > 0){
			return (int)$result[0]->count;
		}

		return -1;
	}

}
