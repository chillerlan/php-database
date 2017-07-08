<?php
/**
 * Class PDOMSSqlSrvQueryTest
 *
 * @filesource   PDOMSSqlSrvQueryTest.php
 * @created      08.07.2017
 * @package      chillerlan\DatabaseTest\Query\MSSqlSrv
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query\MSSqlSrv;

use chillerlan\Database\Drivers\PDO\PDOMSSqlSrvDriver;

class PDOMSSqlSrvQueryTest extends MSSqlSrvQueryTestAbstract{

	protected $driver = PDOMSSqlSrvDriver::class;

}
