<?php
/**
 * Class PDOMSSqlSrvDriverTest
 *
 * @filesource   PDOMSSqlSrvDriverTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Drivers\PDO
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers\PDO;

use chillerlan\Database\Drivers\PDO\PDOMSSqlSrvDriver;

class PDOMSSqlSrvDriverTest extends PDOTestAbstract{

	protected $driver = PDOMSSqlSrvDriver::class;
	protected $envVar = 'DB_MSSQL_';

	protected $SQL_RAW_CREATE     = 'CREATE TABLE test (id INT NOT NULL, hash VARCHAR(32) NOT NULL)';
	protected $SQL_RAW_TRUNCATE   = 'TRUNCATE TABLE test';

}
