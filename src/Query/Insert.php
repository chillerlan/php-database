<?php
/**
 * Class Insert
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use chillerlan\Database\ResultInterface;
use function array_keys;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/insert.html
 * @link https://www.postgresql.org/docs/current/static/sql-insert.html
 * @link https://docs.microsoft.com/en-gb/sql/t-sql/statements/insert-transact-sql
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-dml-insert.html
 * @link https://www.sqlite.org/lang_insert.html
 */
class Insert extends Statement implements BindValues, MultiQuery{

	public function into(string $table, string|null $on_conflict = null, string|null $conflict_target = null):static{
		return $this->setOnConflict($table, $on_conflict, $conflict_target);
	}

	public function values(iterable $values):static{

		if($values instanceof ResultInterface){
			$this->bindValues = $values->toArray();

			return $this;
		}

		foreach($values as $key => $value){
			$this->addBindValue($key, $value);
		}

		return $this;
	}

	/** @inheritdoc */
	protected function getSQL():array{

		if(empty($this->bindValues)){
			throw new QueryException('no values given');
		}

		return $this->dialect->insert(
			$this->name,
			array_keys($this->multi ? $this->bindValues[0] : $this->bindValues),
			$this->on_conflict,
			$this->conflict_target
		);
	}

}
