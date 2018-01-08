<?php
/**
 * Class MySQLPDOTest
 *
 * @filesource   MySQLPDOTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Drivers
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\MySQLPDO;

class MySQLPDOTest extends DriverTestAbstract{

	protected $driver = MySQLPDO::class;
	protected $envVar = 'DB_MYSQLI_';

	protected $SQL_RAW_ERROR      = '';
	protected $SQL_RAW_TRUNCATE   = 'TRUNCATE TABLE test';

}
