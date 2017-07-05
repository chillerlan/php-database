<?php
/**
 * Class PDOFirebirdDriverTest
 *
 * @filesource   PDOFirebirdDriverTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Drivers\PDO
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers\PDO;

use chillerlan\Database\Drivers\PDO\PDOFirebirdDriver;

class PDOFirebirdDriverTest extends PDOTestAbstract{

	protected $driver = PDOFirebirdDriver::class;
	protected $envVar = 'DB_FIREBIRD_';

	protected $SQL_RAW_DROP        = 'DROP TABLE "test"';
	protected $SQL_RAW_CREATE      = 'RECREATE TABLE "test" ("id" INTEGER NOT NULL, "hash" VARCHAR(32) NOT NULL)';
	protected $SQL_RAW_TRUNCATE    = 'RECREATE TABLE "test" ("id" INTEGER NOT NULL, "hash" VARCHAR(32) NOT NULL)';
	protected $SQL_RAW_SELECT_ALL  = 'SELECT * FROM "test"';
	protected $SQL_RAW_INSERT      = 'INSERT INTO "test" ("id", "hash") VALUES (%1$d, \'%2$s\')';
	protected $SQL_PREPARED_INSERT = 'INSERT INTO "test" ("id", "hash") VALUES (?, ?)';

}
