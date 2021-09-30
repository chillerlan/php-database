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
use function get_resource_type;

final class PostgreSQLTest extends DriverTestAbstract{

	protected string $envPrefix  = 'DB_POSTGRES';
	protected string $driverFQCN = PostgreSQL::class;

	public function testGetDBResource():void{
		$r = $this->driver->getDBResource();

		$this->assertIsResource($r);
		$this->assertSame('pgsql link', get_resource_type($r));
	}

	public function testEscapeString():void{
		// https://xkcd.com/327/
		$this->assertSame(
			"encode(decode('526f6265727427293b2044524f50205441424c452053747564656e74733b202d2d', 'hex'), 'escape')",
			$this->driver->escape( "Robert'); DROP TABLE Students; --")
		);
	}


}
