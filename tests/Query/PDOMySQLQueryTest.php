<?php
/**
 * Class PDOMySQLQueryTest
 *
 * @filesource   PDOMySQLQueryTest.php
 * @created      29.06.2017
 * @package      chillerlan\DatabaseTest\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query;

use chillerlan\Database\Drivers\MySQLPDO;

class PDOMySQLQueryTest extends MySQLQueryTestAbstract{

	protected $driver = MySQLPDO::class;

}
