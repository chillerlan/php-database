<?php
/**
 * Interface DropItem
 *
 * @filesource   DropItem.php
 * @created      22.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

/**
 * @method string sql(bool $multi = null)
 * @method mixed  query(string $index = null)
 */
interface DropItem extends Statement{

	/**
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\DropItem
	 */
	public function name(string $tablename);

	/**
	 * @return \chillerlan\Database\Query\DropItem
	 */
	public function ifExists();

}
