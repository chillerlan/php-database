<?php
/**
 * Class MySQLNativeQueryTest
 *
 * @filesource   MySQLNativeQueryTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query;

use chillerlan\Database\Drivers\MySQLiDrv;

class MySQLNativeQueryTest extends MySQLQueryTestAbstract{

	protected $driver = MySQLiDrv::class;

}
