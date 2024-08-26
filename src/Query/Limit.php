<?php
/**
 * Interface Limit
 *
 * @created      01.11.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

interface Limit{

	public function limit(int $limit):static;

	public function offset(int $offset):static;

}
