<?php
/**
 * Interface CreateDatabase
 *
 * @filesource   CreateDatabase.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/create-database.html
 * @link https://www.postgresql.org/docs/current/static/sql-createdatabase.html
 * @link https://msdn.microsoft.com/library/ms176061(v=sql.110).aspx
 *
 * @method string sql(bool $multi = null)
 * @method array  getBindValues()
 * @method mixed  query(string $index = null)
 */
interface CreateDatabase extends Statement{

	/**
	 * @return \chillerlan\Database\Query\CreateDatabase
	 */
	public function ifNotExists();

	/**
	 * @param string $dbname
	 *
	 * @return \chillerlan\Database\Query\CreateDatabase
	 */
	public function name(string $dbname);

	/**
	 * @param string $collation
	 *
	 * @return \chillerlan\Database\Query\CreateDatabase
	 */
	public function charset(string $collation);

}
