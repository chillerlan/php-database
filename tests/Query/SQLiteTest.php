<?php
/**
 * Class SQLiteTest
 *
 * @created      02.11.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query;

use chillerlan\Database\Drivers\PDOSQLite;
use chillerlan\Database\ResultInterface;

class SQLiteTest extends QueryTestAbstract{

	protected string $envPrefix  = 'DB_SQLITE3';
	protected string $driverFQCN = PDOSQLite::class;

	protected function assertInsertResult(ResultInterface $result):void{
		$row = $result[0];

		$this::assertSame('0', $row->id);
		$this::assertSame('foo', $row->data);
		$this::assertSame('123.456', $row->value);
		$this::assertSame('1', $row->active);
	}

	protected function assertInsertMultiResult(ResultInterface $result):void{
		$this::assertSame(3, $result->count());

		$this::assertSame('3', $result[2]->id);

		$this::assertSame('123.456', $result[0]->value);
		$this::assertSame('123.456789', $result[1]->value);

		$this::assertSame('0', $result[0]->active);
		$this::assertSame('1', $result[1]->active);
	}

	public function testShowDatabases():void{
		$this->markTestSkipped('not supported');
	}

}
