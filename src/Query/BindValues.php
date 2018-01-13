<?php
/**
 * Interface BindValues
 *
 * @filesource   BindValues.php
 * @created      10.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

interface BindValues{

	/**
	 * @return array
	 */
	public function getBindValues():array;

}
