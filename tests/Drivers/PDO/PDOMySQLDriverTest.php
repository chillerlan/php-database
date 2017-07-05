<?php
/**
 *
 * @filesource   PDOMySQLDriverTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Drivers\PDO
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers\PDO;

use chillerlan\Database\Drivers\PDO\PDOMySQLDriver;

/**
 * Class PDOMySQLDriverTest
 */
class PDOMySQLDriverTest extends PDOTestAbstract{

	protected $driver = PDOMySQLDriver::class;
	protected $envVar = 'DB_MYSQLI_';

	protected $SQL_RAW_ERROR      = '';
	protected $SQL_RAW_TRUNCATE   = 'TRUNCATE TABLE test';

}
