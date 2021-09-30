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

final class PDOSQLiteTest extends PDODriverTestAbstract{

	protected string $envPrefix  = 'DB_SQLITE3';
	protected string $driverFQCN = PDOSQLite::class;

}
