<?php
/**
 * Class MySQLiTest
 *
 * @created      21.04.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
 */

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\MySQLiDrv;
use mysqli;

final class MySQLiTest extends DriverTestAbstract{

	protected string $envPrefix  = 'DB_MYSQLI';
	protected string $driverFQCN = MySQLiDrv::class;

	public function testGetDBResource():void{
		$this->assertInstanceOf(mysqli::class, $this->driver->getDBResource());
	}

}
