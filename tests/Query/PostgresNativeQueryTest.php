<?php
/**
 * Class PostgresNativeQueryTest
 *
 * @filesource   PostgresNativeQueryTest.php
 * @created      29.06.2017
 * @package      chillerlan\DatabaseTest\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query;

use chillerlan\Database\Drivers\PostgreSQL;

class PostgresNativeQueryTest extends QueryTestAbstract{

	protected $envVar = 'DB_POSTGRES_';
	protected $driver = PostgreSQL::class;

}
