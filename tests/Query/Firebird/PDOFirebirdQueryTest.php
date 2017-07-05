<?php
/**
 * Class PDOFirebirdQueryTest
 *
 * @filesource   PDOFirebirdQueryTest.php
 * @created      29.06.2017
 * @package      chillerlan\DatabaseTest\Query\Firebird
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query\Firebird;

use chillerlan\Database\Drivers\PDO\PDOFirebirdDriver;

class PDOFirebirdQueryTest extends FirebirdQueryTestAbstract{

	protected $driver = PDOFirebirdDriver::class;

}
