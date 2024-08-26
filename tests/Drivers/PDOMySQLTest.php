<?php
/**
 * Class PDOMySQLTest
 *
 * @created      21.04.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\PDOMySQL;
use PHPUnit\Framework\Attributes\Group;
use function extension_loaded;

#[Group('mysql')]
final class PDOMySQLTest extends PDODriverTestAbstract{

	protected string $envPrefix  = 'DB_MYSQLI';
	protected string $driverFQCN = PDOMySQL::class;

	protected function setUp():void{

		if(!extension_loaded('pdo_mysql')){
			$this::markTestSkipped('mysql (PDO) not installed');
		}

		parent::setUp();
	}

}
