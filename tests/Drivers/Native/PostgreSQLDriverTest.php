<?php
/**
 * Class PostgreSQLDriverTest
 *
 * @filesource   PostgreSQLDriverTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Drivers\Native
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers\Native;

use chillerlan\Database\Drivers\Native\PostgreSQLDriver;
use chillerlan\DatabaseTest\Drivers\DriverTestAbstract;

class PostgreSQLDriverTest extends DriverTestAbstract{

	protected $driver = PostgreSQLDriver::class;
	protected $envVar = 'DB_POSTGRES_';

	protected $SQL_PREPARED_INSERT = 'INSERT INTO test (id, hash) VALUES ($1, $2)';

}
