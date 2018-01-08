<?php
/**
 * Class SQLitePDOMemoryTest
 *
 * @filesource   SQLitePDOMemoryTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Drivers
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers;

class SQLitePDOMemoryTest extends SQLitePDOTest{

	protected function setUp(){
		parent::setUp();

		$this->options->database = ':memory:';
		$this->db = (new $this->driver($this->options, $this->cache))->connect();
	}

}
