<?php
/**
 * Class PostgreSQLTest
 *
 * @created      21.04.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\PostgreSQL;
use PHPUnit\Framework\Attributes\Group;
use function extension_loaded;
use function get_resource_type;
use const PHP_VERSION_ID;

#[Group('pgsql')]
final class PostgreSQLTest extends DriverTestAbstract{

	protected string $envPrefix  = 'DB_POSTGRES';
	protected string $driverFQCN = PostgreSQL::class;

	protected function setUp():void{

		if(!extension_loaded('pgsql')){
			$this::markTestSkipped('pgsql not installed');
		}

		parent::setUp();
	}

	public function testGetDBResource():void{
		$r = $this->driver->getDBResource();

		if(PHP_VERSION_ID >= 80100){
			$this::assertInstanceOf('PgSql\\Connection', $r);
		}
		else{
			$this::assertIsResource($r);
			$this::assertSame('pgsql link', get_resource_type($r));
		}
	}

	public function testEscapeString():void{
		// https://xkcd.com/327/
		$this::assertSame(
			"encode(decode('526f6265727427293b2044524f50205441424c452053747564656e74733b202d2d', 'hex'), 'escape')",
			$this->driver->escape( "Robert'); DROP TABLE Students; --")
		);
	}


}
