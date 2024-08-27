<?php
/**
 * Class Update
 *
 * @created      03.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

use function array_merge, is_bool, is_int, is_scalar;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/update.html
 * @link https://www.postgresql.org/docs/current/static/sql-update.html
 * @link https://docs.microsoft.com/de-de/sql/t-sql/queries/update-transact-sql
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-dml-update.html
 * @link https://www.sqlite.org/lang_update.html
 */
class Update extends StatementAbstract implements Where, Limit, BindValues, MultiQuery{

	protected array $set = [];

	public function table(string $name):static{
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

	protected function sql():array{

		if(empty($this->set)){
			throw new QueryException('no fields to update specified');
		}

		return $this->dialect->update($this->name, $this->set, $this->getWhere());
	}

	public function set(array $set, bool|null $bind = null):static{

		foreach($set as $k => $v){

			if($v instanceof Query){
				$this->set[] = $this->dialect->quote($k).' = ('.$v->getSQL().')';

				if($v instanceof BindValues){
					$this->bindValues = array_merge($this->bindValues, $v->getBindValues());
				}
			}
			elseif(!is_scalar($v)){ // is_array($v)
				// @todo: [expr, bindval1, bindval2, ...]
				continue;
			}
			else{
				if($bind === false){
					// here be dragons
					$this->set[] = is_int($k)
						? $this->dialect->quote($v).' = ?'
						: $this->dialect->quote($k).' = '.$v; //$this->db->escape($v)
				}
				else{
					$this->set[] = $this->dialect->quote($k).' = ?';
					$this->addBindValue($k, is_bool($v) ? (int)$v : $v);// avoid errors with PDO firebird & mysql
				}
			}
		}

		return $this;
	}

}
