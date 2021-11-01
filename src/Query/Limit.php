<?php
/**
 * Interface Limit
 *
 * @created      01.11.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

interface Limit{

	/**
	 * @return $this
	 */
	public function limit(int $limit);

	/**
	 * @return $this
	 */
	public function offset(int $offset);

}
