<?php
/**
 * Class AlterDatabase
 *
 * @created      09.01.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/alter-database.html
 * @link https://www.postgresql.org/docs/current/static/sql-alterdatabase.html
 * @link https://docs.microsoft.com/en-gb/sql/t-sql/statements/alter-database-transact-sql
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-ddl-db.html#fblangref25-ddl-db-alter
 */
class AlterDatabase extends Statement implements Query{

	public function name(string $dbname):static{
		return $this->setName($dbname);
	}

	protected function getSQL():array{
		return []; // @todo
	}

}
