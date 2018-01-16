<?php
/**
 * Interface ShowItem
 *
 * @filesource   ShowItem.php
 * @created      15.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

/**
 * @method \chillerlan\Database\Query\ShowItem where($val1, $val2, string $operator = null, bool $bind = null, string $join = null)
 * @method \chillerlan\Database\Query\ShowItem openBracket(string $join = null)
 * @method \chillerlan\Database\Query\ShowItem closeBracket()
 * @method \chillerlan\Database\Query\ShowItem from(string $table)
 * @method array  getBindValues()
 * @method string sql(bool $multi = null)
 * @method mixed  query(string $index = null)
 */
interface ShowItem extends Statement{

}
