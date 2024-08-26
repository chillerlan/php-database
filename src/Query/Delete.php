<?php
/**
 * Class Delete
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/delete.html
 * @link https://www.postgresql.org/docs/current/static/sql-delete.html
 * @link https://msdn.microsoft.com/de-de/library/ms189835(v=sql.110).aspx
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-dml-delete.html
 * @link https://www.sqlite.org/lang_delete.html
 */
class Delete extends Statement implements Where, Limit, BindValues, Query{

	public function from(string $name):static{
		return $this->setName($name);
	}

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

	protected function getSQL():array{
		return $this->dialect->delete($this->name, $this->getWhere());
	}

}
