<?php
/**
 * Class MySQLiDriverTest
 *
 * @filesource   MySQLiDriverTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Drivers\Native
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers\Native;

use chillerlan\Database\Drivers\Native\MySQLiDriver;
use chillerlan\DatabaseTest\Drivers\DriverTestAbstract;

class MySQLiDriverTest extends DriverTestAbstract{

	protected $driver = MySQLiDriver::class;
	protected $envVar = 'DB_MYSQLI_';

	protected $SQL_RAW_ERROR      = '';
	protected $SQL_RAW_TRUNCATE   = 'TRUNCATE TABLE test';

}
