<?php
/**
 * Class PDOPostgresQueryTest
 *
 * @filesource   PDOPostgresQueryTest.php
 * @created      29.06.2017
 * @package      chillerlan\DatabaseTest\Query\Postgres
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query\Postgres;

use chillerlan\Database\Drivers\PDO\PDOPostgresDriver;

class PDOPostgresQueryTest extends PostgresQueryTestAbstract{

	protected $driver = PDOPostgresDriver::class;

}
