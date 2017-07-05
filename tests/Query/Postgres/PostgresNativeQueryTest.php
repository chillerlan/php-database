<?php
/**
 * Class PostgresNativeQueryTest
 *
 * @filesource   PostgresNativeQueryTest.php
 * @created      29.06.2017
 * @package      chillerlan\DatabaseTest\Query\Postgres
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query\Postgres;

use chillerlan\Database\Drivers\Native\PostgreSQLDriver;

class PostgresNativeQueryTest extends PostgresQueryTestAbstract{

	protected $driver = PostgreSQLDriver::class;

}
