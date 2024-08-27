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
declare(strict_types=1);

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\MySQLiDrv;
use mysqli;
use PHPUnit\Framework\Attributes\Group;
use function extension_loaded;

#[Group('mysql')]
final class MySQLiTest extends DriverTestAbstract{

	protected string $envPrefix  = 'DB_MYSQLI';
	protected string $driverFQCN = MySQLiDrv::class;

	protected function setUp():void{

		if(!extension_loaded('mysqli')){
			$this::markTestSkipped('mysqli not installed');
		}

		parent::setUp();
	}

	public function testGetDBResource():void{
		$this::assertInstanceOf(mysqli::class, $this->db->getDBResource());
	}

}
