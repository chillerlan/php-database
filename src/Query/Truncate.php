<?php
/**
 * Interface Truncate
 *
 * @filesource   Truncate.php
 * @created      09.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/truncate-table.html
 * @link https://www.postgresql.org/docs/current/static/sql-truncate.html
 * @link https://docs.microsoft.com/en-us/sql/t-sql/statements/truncate-table-transact-sql
 *
 * @method string sql(bool $multi = null)
 * @method mixed  query(string $index = null)
 */
interface Truncate extends Statement{

	/**
	 * @param string $table
	 *
	 * @return \chillerlan\Database\Query\Truncate
	 */
	public function table(string $table);

}
