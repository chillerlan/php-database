<?php
/**
 * Class PDOFirebirdTest
 *
 * @created      22.04.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\PDOFirebird;
use function extension_loaded;

final class PDOFirebirdTest extends PDODriverTestAbstract{

	protected string $envPrefix  = 'DB_FIREBIRD';
	protected string $driverFQCN = PDOFirebird::class;

	protected function setUp():void{

		if(!extension_loaded('pdo_firebird')){
			$this::markTestSkipped('firebird not installed');
		}

		parent::setUp();
	}

}
