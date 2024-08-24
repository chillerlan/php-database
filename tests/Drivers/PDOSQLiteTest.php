<?php
/**
 * Class PDOSQLiteTest
 *
 * @created      21.04.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\PDOSQLite;
use PHPUnit\Framework\Attributes\Group;
use function extension_loaded;

#[Group('sqlite')]
final class PDOSQLiteTest extends PDODriverTestAbstract{

	protected string $envPrefix  = 'DB_SQLITE3';
	protected string $driverFQCN = PDOSQLite::class;

	protected function setUp():void{

		if(!extension_loaded('pdo_sqlite')){
			$this::markTestSkipped('sqlite (PDO) not installed');
		}

		parent::setUp();
	}

}
