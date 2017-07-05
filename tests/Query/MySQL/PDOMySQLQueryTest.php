<?php
/**
 * Class PDOMySQLQueryTest
 *
 * @filesource   PDOMySQLQueryTest.php
 * @created      29.06.2017
 * @package      chillerlan\DatabaseTest\Query\MySQL
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query\MySQL;

use chillerlan\Database\Drivers\PDO\PDOMySQLDriver;

class PDOMySQLQueryTest extends MySQLQueryTestAbstract{

	protected $driver = PDOMySQLDriver::class;

}
