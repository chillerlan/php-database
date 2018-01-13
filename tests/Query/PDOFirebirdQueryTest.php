<?php
/**
 * Class PDOFirebirdQueryTest
 *
 * @filesource   PDOFirebirdQueryTest.php
 * @created      29.06.2017
 * @package      chillerlan\DatabaseTest\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query;

use chillerlan\Database\Drivers\FirebirdPDO;

class PDOFirebirdQueryTest extends QueryTestAbstract{

	protected $driver = FirebirdPDO::class;
	protected $envVar = 'DB_FIREBIRD_';

	public function setUp(){$this->markTestSkipped('use the vagrant box...');}

	/**
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage not supported
	 */
	public function testCreateDatabase(){
		$this->query->create->database('foo')->sql();
	}


}
