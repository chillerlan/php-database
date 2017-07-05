<?php
/**
 * Class MySQLNativeQueryTest
 *
 * @filesource   MySQLNativeQueryTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Query\MySQL
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query\MySQL;

use chillerlan\Database\Drivers\Native\MySQLiDriver;

class MySQLNativeQueryTest extends MySQLQueryTestAbstract{

	protected $driver = MySQLiDriver::class;

}
