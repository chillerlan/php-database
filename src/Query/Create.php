<?php
/**
 * Interface Create
 *
 * @filesource   Create.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Create
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

/**
 * @link https://www.postgresql.org/docs/current/static/datatype.html
 * @link https://docs.microsoft.com/sql/t-sql/data-types/data-types-transact-sql
 */
interface Create extends Statement{

#	public function index():Create;
#	public function view():Create;
#	public function trigger():Create;

	/**
	 * @link https://dev.mysql.com/doc/refman/5.7/en/create-database.html
	 * @link https://www.postgresql.org/docs/current/static/sql-createdatabase.html
	 * @link https://docs.microsoft.com/en-gb/sql/t-sql/statements/create-database-sql-server-transact-sql
	 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-ddl-db.html#fblangref25-ddl-db-create
	 *
	 * @param string|null $dbname
	 *
	 * @return \chillerlan\Database\Query\CreateDatabase
	 */
	public function database(string $dbname):CreateDatabase;

	/**
	 * @link https://www.sqlite.org/lang_createtable.html
	 * @link https://dev.mysql.com/doc/refman/5.7/en/create-table.html
	 * @link https://www.postgresql.org/docs/current/static/sql-createtable.html
	 * @link https://docs.microsoft.com/en-gb/sql/t-sql/statements/create-table-transact-sql
	 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-ddl-tbl.html#fblangref25-ddl-tbl-create
	 *
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\CreateTable
	 */
	public function table(string $tablename):CreateTable;

}
