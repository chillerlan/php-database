<?php
/**
 * Class PDODriverTestAbstract
 *
 * @created      22.04.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
 */

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\DriverException;
use PDO;

abstract class PDODriverTestAbstract extends DriverTestAbstract{

	public function testGetDBResource():void{
		$this->assertInstanceOf(PDO::class, $this->driver->getDBResource());
	}

	public function testGetDsnNoDatabaseException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('no database given');

		$this->options->database = null;

		$this->driver = new $this->driverFQCN($this->options);

		$this->getMethod('getDSN')->invoke($this->driver);
	}

}
