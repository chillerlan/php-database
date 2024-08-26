<?php
/**
 * Class CreateDatabase
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/create-database.html
 * @link https://www.postgresql.org/docs/current/static/sql-createdatabase.html
 * @link https://docs.microsoft.com/en-gb/sql/t-sql/statements/create-database-sql-server-transact-sql
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-ddl-db.html#fblangref25-ddl-db-create
 */
class CreateDatabase extends Statement implements Query, IfNotExists{

	public function name(string $dbname):static{
		return $this->setName($dbname);
	}

	public function charset(string $collation):static{
		return $this->setCharset($collation);
	}

	public function ifNotExists():static{
		return $this->setIfNotExists();
	}

	/** @inheritdoc */
	protected function getSQL():array{
		return $this->dialect->createDatabase($this->name, $this->ifNotExists, $this->charset);
	}

}
