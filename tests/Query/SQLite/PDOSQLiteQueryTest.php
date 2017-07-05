<?php
/**
 * Class PDOSQLiteQueryTest
 *
 * @filesource   PDOSQLiteQueryTest.php
 * @created      29.06.2017
 * @package      chillerlan\DatabaseTest\Query\SQLite
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query\SQLite;

use chillerlan\Database\Drivers\PDO\PDOSQLiteDriver;
use chillerlan\Database\Query\Dialects\SQLiteQueryBuilder;
use chillerlan\DatabaseTest\Query\QueryTestAbstract;

class PDOSQLiteQueryTest extends QueryTestAbstract{

	protected $driver      = PDOSQLiteDriver::class;
	protected $querydriver = SQLiteQueryBuilder::class;
	protected $envVar      = 'DB_SQLITE3_';

	public function testCreateDatabase(){
		$this->assertSame(
			'--NOT SUPPORTED',
			$this->createDatabase()->sql()
		);
	}

	public function testCreateDatabaseNoName(){
		$this->markTestSkipped('[sqlite] create database not supported');
	}


}
