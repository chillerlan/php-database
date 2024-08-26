<?php
/**
 * Class PDOMSSqlSrvTest
 *
 * @created      21.04.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\PDOMSSqlSrv;
use PHPUnit\Framework\Attributes\Group;
use function function_exists;

#[Group('mssql')]
final class PDOMSSqlSrvTest extends PDODriverTestAbstract{

	protected string $envPrefix  = 'DB_MSSQL';
	protected string $driverFQCN = PDOMSSqlSrv::class;

	protected function setUp():void{

		if(!function_exists('sqlsrv_connect')){
			$this::markTestSkipped('mssql not installed');
		}

		parent::setUp();
	}

	public function testEscapeString():void{
		// https://xkcd.com/327/
		$this::assertSame(
			'0x526f6265727427293b2044524f50205441424c452053747564656e74733b202d2d',
			$this->driver->escape("Robert'); DROP TABLE Students; --")
		);
	}


}
