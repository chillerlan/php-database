<?php
/**
 * Class MSSqlSrvQueryTestAbstract
 *
 * @filesource   MSSqlSrvQueryTestAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query;

abstract class MSSqlSrvQueryTestAbstract extends QueryTestAbstract{

	protected $envVar      = 'DB_MSSQL_';

#	public function setUp(){$this->markTestSkipped('use the vagrant box...');}

	public function testCreateDatabase(){
		$this->assertSame(
			'CREATE DATABASE [vagrant] COLLATE utf8',
			$this->createDatabase()->sql()
		);

		$this->assertSame(
			'CREATE DATABASE [vagrant] COLLATE Latin1_General_CI_AS',
			$this->createDatabase()->charset('Latin1_General_CI_AS')->ifNotExists()->sql()
		);
	}

}
