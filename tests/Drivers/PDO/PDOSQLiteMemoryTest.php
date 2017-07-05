<?php
/**
 *
 * @filesource   PDOSQLiteMemoryTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Drivers\PDO
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers\PDO;

/**
 * Class PDOSQLiteMemoryTest
 */
class PDOSQLiteMemoryTest extends PDOSQLiteDriverTest{

	protected function setUp(){
		parent::setUp();

		$this->options->database = ':memory:';
		$this->db = (new $this->driver($this->options, $this->cache))->connect();
	}

}
