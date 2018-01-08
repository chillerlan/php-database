<?php
/**
 * Class SQLitePDOTest
 *
 * @filesource   SQLitePDOTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Drivers
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\SQLitePDO;

class SQLitePDOTest extends DriverTestAbstract{

	protected $driver = SQLitePDO::class;
	protected $envVar = 'DB_SQLITE3_';

}
