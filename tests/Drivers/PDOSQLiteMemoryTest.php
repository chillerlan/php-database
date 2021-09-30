<?php
/**
 * Class PDOSQLiteMemoryTest
 *
 * @created      21.04.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\PDOSQLite;

final class PDOSQLiteMemoryTest extends PDODriverTestAbstract{

	protected string $envPrefix  = 'SQLITE_MEM';
	protected string $driverFQCN = PDOSQLite::class;

}
