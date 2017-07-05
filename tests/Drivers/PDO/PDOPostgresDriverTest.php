<?php
/**
 * Class PDOPostgresDriverTest
 *
 * @filesource   PDOPostgresDriverTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Drivers\PDO
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers\PDO;

use chillerlan\Database\Drivers\PDO\PDOPostgresDriver;

class PDOPostgresDriverTest extends PDOTestAbstract{

	protected $driver = PDOPostgresDriver::class;
	protected $envVar = 'DB_POSTGRES_';

}
