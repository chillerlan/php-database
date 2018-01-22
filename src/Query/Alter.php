<?php
/**
 * Interface Alter
 *
 * @filesource   Alter.php
 * @created      15.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

interface Alter extends Statement{

	/**
	 * @link https://dev.mysql.com/doc/refman/5.7/en/alter-table.html
	 * @link https://www.postgresql.org/docs/current/static/sql-altertable.html
	 * @link https://docs.microsoft.com/en-gb/sql/t-sql/statements/alter-table-transact-sql
	 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-ddl-tbl.html#fblangref25-ddl-tbl-alter
	 * @link https://www.sqlite.org/lang_altertable.html
	 *
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\AlterTable
	 */
	public function table(string $tablename):AlterTable;

	/**
	 * @link https://dev.mysql.com/doc/refman/5.7/en/alter-database.html
	 * @link https://www.postgresql.org/docs/current/static/sql-alterdatabase.html
	 * @link https://docs.microsoft.com/en-gb/sql/t-sql/statements/alter-database-transact-sql
	 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-ddl-db.html#fblangref25-ddl-db-alter
	 *
	 * @param string $dbname
	 *
	 * @return \chillerlan\Database\Query\AlterDatabase
	 */
	public function database(string $dbname):AlterDatabase;

}
