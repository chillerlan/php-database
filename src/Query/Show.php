<?php
/**
 * Interface Show
 *
 * @filesource   Show.php
 * @created      09.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;


/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/show.html
 *
 * @property \chillerlan\Database\Query\ShowCreate create
 */
interface Show extends Statement{

	/**
	 * @return \chillerlan\Database\Query\ShowCreate
	 */
	public function create():ShowCreate;

	/**
	 * @return \chillerlan\Database\Query\ShowItem
	 */
	public function databases():ShowItem;

	/**
	 * @param string|null $from
	 *
	 * @return \chillerlan\Database\Query\ShowItem
	 */
	public function tables(string $from = null):ShowItem;
#	public function columns():ShowItem;
#	public function index():ShowItem;
#	public function collation():ShowItem;
#	public function characterSet():ShowItem;
#	public function tableStatus():ShowItem;

}
