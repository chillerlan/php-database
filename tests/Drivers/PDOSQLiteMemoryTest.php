<?php
/**
 * Class PDOSQLiteMemoryTest
 *
 * @created      21.04.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\PDOSQLite;
use PHPUnit\Framework\Attributes\Group;
use function extension_loaded;

#[Group('sqlite')]
final class PDOSQLiteMemoryTest extends PDODriverTestAbstract{

	protected string $envPrefix  = 'SQLITE_MEM';
	protected string $driverFQCN = PDOSQLite::class;

	protected function setUp():void{

		if(!extension_loaded('pdo_sqlite')){
			$this::markTestSkipped('sqlite (PDO) not installed');
		}

		parent::setUp();
	}

}
