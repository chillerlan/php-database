<?php
/**
 * Class PDOSQLiteQueryTest
 *
 * @filesource   PDOSQLiteQueryTest.php
 * @created      29.06.2017
 * @package      chillerlan\DatabaseTest\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query;

use chillerlan\Database\Drivers\SQLitePDO;

class PDOSQLiteQueryTest extends QueryTestAbstract{

	protected $driver      = SQLitePDO::class;
	protected $envVar      = 'DB_SQLITE3_';

	/**
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage not supported
	 */
	public function testCreateDatabase(){
		$this->query->create->database('foo')->sql();
	}

}
