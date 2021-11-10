<?php
/**
 * Interface CachedQuery
 *
 * @created      01.11.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

interface CachedQuery{

	public function cached(int $ttl = null):self;

}
