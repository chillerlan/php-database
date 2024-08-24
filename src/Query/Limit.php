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

	public function limit(int $limit):static;

	public function offset(int $offset):static;

}
