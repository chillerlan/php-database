<?php
/**
 * Class FirebirdPDOTest
 *
 * @filesource   FirebirdPDOTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Drivers
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\FirebirdPDO;

class FirebirdPDOTest extends DriverTestAbstract{

	protected $driver = FirebirdPDO::class;
	protected $envVar = 'DB_FIREBIRD_';

	protected $SQL_RAW_DROP        = 'DROP TABLE "test"';
	protected $SQL_RAW_CREATE      = 'RECREATE TABLE "test" ("id" INTEGER NOT NULL, "hash" VARCHAR(32) NOT NULL)';
	protected $SQL_RAW_TRUNCATE    = 'RECREATE TABLE "test" ("id" INTEGER NOT NULL, "hash" VARCHAR(32) NOT NULL)';
	protected $SQL_RAW_SELECT_ALL  = 'SELECT * FROM "test"';
	protected $SQL_RAW_INSERT      = 'INSERT INTO "test" ("id", "hash") VALUES (%1$d, \'%2$s\')';
	protected $SQL_PREPARED_INSERT = 'INSERT INTO "test" ("id", "hash") VALUES (?, ?)';

	public function setUp(){$this->markTestSkipped('use the vagrant box...');}

}
