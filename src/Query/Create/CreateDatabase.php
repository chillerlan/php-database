<?php
/**
 * Interface CreateDatabase
 *
 * @filesource   CreateDatabase.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Create
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Create;

use chillerlan\Database\Query\Statement;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/create-database.html
 * @link https://www.postgresql.org/docs/current/static/sql-createdatabase.html
 * @link https://msdn.microsoft.com/library/ms176061(v=sql.110).aspx
 */
interface CreateDatabase extends Statement{

	/**
	 * @return \chillerlan\Database\Query\Create\CreateDatabase
	 */
	public function ifNotExists();

	/**
	 * @param string $dbname
	 *
	 * @return \chillerlan\Database\Query\Create\CreateDatabase
	 */
	public function name(string $dbname);

	/**
	 * @param string $collation
	 *
	 * @return \chillerlan\Database\Query\Create\CreateDatabase
	 */
	public function charset(string $collation);

}
