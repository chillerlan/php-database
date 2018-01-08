<?php
/**
 * Class PDOMSSqlSrvQueryTest
 *
 * @filesource   PDOMSSqlSrvQueryTest.php
 * @created      08.07.2017
 * @package      chillerlan\DatabaseTest\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query;

use chillerlan\Database\Drivers\MSSqlSrvPDO;

abstract class PDOMSSqlSrvQueryTest extends MSSqlSrvQueryTestAbstract{

	protected $driver = MSSqlSrvPDO::class;

}
