<?php
/**
 * Class FirebirdQueryTestAbstract
 *
 * @filesource   FirebirdQueryTestAbstract.php
 * @created      29.06.2017
 * @package      chillerlan\DatabaseTest\Query\Firebird
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query\Firebird;

use chillerlan\Database\Query\Dialects\FirebirdQueryBuilder;
use chillerlan\DatabaseTest\Query\QueryTestAbstract;

abstract class FirebirdQueryTestAbstract extends QueryTestAbstract{

	protected $querydriver = FirebirdQueryBuilder::class;
	protected $envVar = 'DB_FIREBIRD_';

#	public function setUp(){
#		$this->markTestSkipped('use the vagrant box...');
#	}

	public function testCreateDatabase(){
		$this->assertSame(
			'CREATE DATABASE "vagrant" DEFAULT CHARACTER SET UTF8',
			$this->createDatabase()->sql()
		);

		$this->assertSame(
			'CREATE DATABASE "vagrant" DEFAULT CHARACTER SET UTF8 COLLATION UNICODE_CI_AI',
			$this->createDatabase()->charset('utf8_UNICODE_CI_AI')->sql()
		);
	}


}
