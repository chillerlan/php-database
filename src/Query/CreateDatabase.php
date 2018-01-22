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
