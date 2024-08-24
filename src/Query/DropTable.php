<?php
/**
 * Class DropTable
 *
 * @created      01.11.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/drop-table.html
 * @link https://www.postgresql.org/docs/current/static/sql-droptable.html
 * @link https://docs.microsoft.com/en-gb/sql/t-sql/statements/drop-table-transact-sql
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-ddl-tbl.html#fblangref25-ddl-tbl-drop
 * @link https://www.sqlite.org/lang_droptable.html
 */
class DropTable extends Statement implements Query, IfExists{

	public function name(string $name):static{
		return $this->setName($name);
	}

	public function ifExists():static{
		return $this->setIfExists();
	}

	/** @inheritdoc */
	protected function getSQL():array{
		return $this->dialect->dropTable($this->name, $this->ifExists);
	}

}
