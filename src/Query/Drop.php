<?php
/**
 * Interface Drop
 *
 * @filesource   Drop.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

interface Drop extends Statement{

	/**
	 * @link https://dev.mysql.com/doc/refman/5.7/en/drop-table.html
	 * @link https://www.postgresql.org/docs/current/static/sql-droptable.html
	 * @link https://docs.microsoft.com/en-gb/sql/t-sql/statements/drop-table-transact-sql
	 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-ddl-tbl.html#fblangref25-ddl-tbl-drop
	 * @link https://www.sqlite.org/lang_droptable.html
	 *
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\DropItem
	 */
	public function table(string $tablename):DropItem;

	/**
	 * @link https://dev.mysql.com/doc/refman/5.7/en/drop-database.html
	 * @link https://www.postgresql.org/docs/current/static/sql-dropdatabase.html
	 * @link https://docs.microsoft.com/en-gb/sql/t-sql/statements/drop-database-transact-sql
	 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-ddl-db.html#fblangref25-ddl-db-drop
	 *
	 * @param string $dbname
	 *
	 * @return \chillerlan\Database\Query\DropItem
	 */
	public function database(string $dbname):DropItem;

}
