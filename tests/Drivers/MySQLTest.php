<?php
/**
 * Class MySQLTest
 *
 * @filesource   MySQLTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Drivers\Native
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\MySQLiDrv;

class MySQLTest extends DriverTestAbstract{

	protected $driver = MySQLiDrv::class;
	protected $envVar = 'DB_MYSQLI_';

	protected $SQL_RAW_ERROR      = '';
	protected $SQL_RAW_TRUNCATE   = 'TRUNCATE TABLE test';

}
