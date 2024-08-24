<?php
/**
 * Class DropDatabase
 *
 * @created      01.11.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/drop-database.html
 * @link https://www.postgresql.org/docs/current/static/sql-dropdatabase.html
 * @link https://docs.microsoft.com/en-gb/sql/t-sql/statements/drop-database-transact-sql
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-ddl-db.html#fblangref25-ddl-db-drop
 */
class DropDatabase extends Statement implements Query, IfExists{

	public function name(string $dbname):static{
		return $this->setName($dbname);
	}

	public function ifExists():static{
		return $this->setIfExists();
	}

	/** @inheritdoc */
	protected function getSQL():array{
		return $this->dialect->dropDatabase($this->name, $this->ifExists);
	}

}
