<?php
/**
 * Class PostgreSQLTest
 *
 * @filesource   PostgreSQLTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Drivers\Native
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\PostgreSQL;

class PostgreSQLTest extends DriverTestAbstract{

	protected $driver = PostgreSQL::class;
	protected $envVar = 'DB_POSTGRES_';

	protected $SQL_PREPARED_INSERT = 'INSERT INTO test (id, hash) VALUES ($1, $2)';

}
