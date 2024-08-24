<?php
/**
 * Class PostgresTest
 *
 * @created      02.11.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query;

use chillerlan\Database\Drivers\PostgreSQL;
use chillerlan\Database\ResultInterface;
use PHPUnit\Framework\Attributes\Group;
use function extension_loaded;

#[Group('pgsql')]
final class PostgresTest extends QueryTestAbstract{

	protected string $envPrefix  = 'DB_POSTGRES';
	protected string $driverFQCN = PostgreSQL::class;

	protected function setUp():void{

		if(!extension_loaded('pgsql')){
			$this::markTestSkipped('pgsql not installed');
		}

		parent::setUp();
	}

	protected function assertInsertResult(ResultInterface $result):void{
		$row = $result[0];

		$this::assertSame(0, $row->id);
		$this::assertSame('foo', $row->data);
		$this::assertSame('123.456000', $row->value);
		$this::assertSame(true, $row->active);
	}

	protected function assertInsertMultiResult(ResultInterface $result):void{
		$this::assertSame(3, $result->count());

		$this::assertSame(3, $result[2]->id);

		$this::assertSame('123.456000', $result[0]->value);
		$this::assertSame('123.456789', $result[1]->value);

		$this::assertSame(false, $result[0]->active);
		$this::assertSame(true, $result[1]->active);
	}

}
