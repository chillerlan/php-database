<?php
/**
 * Class MSSQLTest
 *
 * @created      02.11.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\DatabaseTest\Query;

use chillerlan\Database\Drivers\MSSqlSrv;
use chillerlan\Database\ResultInterface;
use PHPUnit\Framework\Attributes\Group;
use function function_exists;

#[Group('mssql')]
final class MSSQLTest extends QueryTestAbstract{

	protected string $envPrefix  = 'DB_MSSQL';
	protected string $driverFQCN = MSSqlSrv::class;

	protected function setUp():void{

		if(!function_exists('sqlsrv_connect')){
			$this::markTestSkipped('mssql not installed');
		}

		parent::setUp();
	}

	protected function assertInsertResult(ResultInterface $result):void{
		$row = $result[0];

		$this::assertSame(0, $row->id);
		$this::assertSame('foo', $row->data);
		$this::assertSame('123.456000', $row->value);
		$this::assertSame(1, $row->active);
	}

	protected function assertInsertMultiResult(ResultInterface $result):void{
		$this::assertSame(3, $result->count());

		$this::assertSame(3, $result[2]->id);

		$this::assertSame('123.456000', $result[0]->value);
		$this::assertSame('123.456789', $result[1]->value);

		$this::assertSame(0, $result[0]->active);
		$this::assertSame(1, $result[1]->active);
	}

}
