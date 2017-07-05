<?php
/**
 * Class InterbaseQueryTest
 *
 * @filesource   InterbaseQueryTest.php
 * @created      29.06.2017
 * @package      chillerlan\DatabaseTest\Query\Firebird
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query\Firebird;

use chillerlan\Database\Drivers\Native\InterbaseDriver;

class InterbaseQueryTest extends FirebirdQueryTestAbstract{

	protected $driver = InterbaseDriver::class;

	public function testInsertMulti(){
		$this->markTestIncomplete();
	}

	public function testSelect(){
		$this->markTestIncomplete();
	}

	public function testUpdate(){
		$this->markTestIncomplete();
	}

	public function testDelete(){
		$this->markTestIncomplete();
	}

}
