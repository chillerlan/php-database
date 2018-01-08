<?php
/**
 * Class PostgresPDOTest
 *
 * @filesource   PostgresPDOTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Drivers
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\PostgreSQLPDO;

class PostgresPDOTest extends DriverTestAbstract{

	protected $driver = PostgreSQLPDO::class;
	protected $envVar = 'DB_POSTGRES_';

}
