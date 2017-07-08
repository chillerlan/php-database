<?php
/**
 * Class InterbaseDriverTest
 *
 * @filesource   InterbaseDriverTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Drivers\Native
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers\Native;

use chillerlan\Database\Drivers\Native\InterbaseDriver;
use chillerlan\DatabaseTest\Drivers\DriverTestAbstract;

class InterbaseDriverTest extends DriverTestAbstract{

	protected $driver = InterbaseDriver::class;
	protected $envVar = 'DB_FIREBIRD_';

	protected $SQL_RAW_DROP        = 'DROP TABLE "test"';
	protected $SQL_RAW_CREATE      = 'RECREATE TABLE "test" ("id" INTEGER NOT NULL, "hash" VARCHAR(32) NOT NULL)';
	protected $SQL_RAW_TRUNCATE    = 'RECREATE TABLE "test" ("id" INTEGER NOT NULL, "hash" VARCHAR(32) NOT NULL)';
	protected $SQL_RAW_SELECT_ALL  = 'SELECT * FROM "test"';
	protected $SQL_RAW_INSERT      = 'INSERT INTO "test" ("id", "hash") VALUES (%1$d, \'%2$s\')';
	protected $SQL_PREPARED_INSERT = 'INSERT INTO "test" ("id", "hash") VALUES (?, ?)';

	public function setUp(){
		$this->markTestSkipped('use the vagrant box...');
	}

	public function testTruncate(){
		$this->markTestSkipped('Dynamic SQL Error SQL error code = -204 Table unknown test At line 1, column 10');
	}

	public function testRaw(){
		$this->markTestSkipped('ibase_close(): unsuccessful metadata update object TABLE "test" is in use <servercrash>');
	}

	public function testPrepared(){
		$this->markTestSkipped('ibase_close(): unsuccessful metadata update object TABLE "test" is in use <servercrash>');
	}

	public function testMulti(){
		$this->markTestIncomplete('not implemented');
	}

	public function testCached(){
		$this->markTestIncomplete('not implemented');
	}

	public function testMultiCallback(){
		$this->markTestIncomplete('not implemented');
	}

	public function testMultiSQLError(){
		$this->markTestIncomplete('not implemented');
	}

	public function testMultiCallbackSQLError(){
		$this->markTestIncomplete('not implemented');
	}

}
