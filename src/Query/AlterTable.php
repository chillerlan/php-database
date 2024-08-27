<?php
/**
 * Class AlterTable
 *
 * @created      09.01.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/alter-table.html
 * @link https://www.postgresql.org/docs/current/static/sql-altertable.html
 * @link https://docs.microsoft.com/en-gb/sql/t-sql/statements/alter-table-transact-sql
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-ddl-tbl.html#fblangref25-ddl-tbl-alter
 * @link https://www.sqlite.org/lang_altertable.html
 */
class AlterTable extends StatementAbstract implements Query{

	public function name(string $tablename):static{
		return $this->setName($tablename);
	}

	protected function getSQL():array{
		return []; // @todo
	}

}
