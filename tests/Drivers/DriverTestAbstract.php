<?php
/**
 * Class DriverTestAbstract
 *
 * @filesource   DriverTestAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Drivers
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\DriverInterface;
use chillerlan\DatabaseTest\DatabaseTestAbstract;

abstract class DriverTestAbstract extends DatabaseTestAbstract{

	/**
	 * @var \chillerlan\Database\Database
	 */
	protected $db;

	protected $SQL_INDEX_COL = 'id';
	protected $SQL_DATA_COL  = 'hash';

	protected $SQL_RAW_ERROR       = 'foo';
	protected $SQL_RAW_DROP        = 'DROP TABLE IF EXISTS test';
	protected $SQL_RAW_CREATE      = 'CREATE TABLE IF NOT EXISTS test (id INTEGER NOT NULL, hash VARCHAR(32) NOT NULL)';
	protected $SQL_RAW_TRUNCATE    = 'DELETE FROM test';
	protected $SQL_RAW_SELECT_ALL  = 'SELECT * FROM test';
	protected $SQL_RAW_INSERT      = 'INSERT INTO test (id, hash) VALUES (%1$d, \'%2$s\')';
	protected $SQL_PREPARED_INSERT = 'INSERT INTO test (id, hash) VALUES (?, ?)';

	protected function setUp(){
		parent::setUp();

		$this->db = $this->connection->getDriverInterface();
	}

	public function testDriverInstance(){
		$this->assertInstanceOf(DriverInterface::class, $this->db);
		$this->assertInstanceOf($this->driver, $this->db);
		$this->assertSame($this->db, $this->db->connect());
		$this->assertNotNull($this->db->getDBResource());

		$info = [
			'driver' => get_class($this->db),
			'client' => $this->db->getClientInfo(),
			'server' => $this->db->getServerInfo(),
		];

		print_r($info);
	}

	/**
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage db error:
	 */
	public function testConnectDBconnectError(){
		$this->options->host     = '...';
		$this->options->database = '...';
		(new $this->driver($this->options))->connect();
	}

	/**
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage sql error:
	 */
	public function testRawSQLError(){
		$this->db->raw($this->SQL_RAW_ERROR);
	}

	/**
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage sql error:
	 */
	public function testPreparedSQLError(){
		$this->db->prepared($this->SQL_RAW_ERROR);
	}

	/**
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage invalid data
	 */
	public function testMultiInvalidData(){
		$this->db->multi($this->SQL_RAW_ERROR, []);
	}

	/**
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage sql error:
	 */
	public function testMultiSQLError(){
		$this->db->multi($this->SQL_RAW_ERROR, [[0]]);
	}

	/**
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage invalid data
	 */
	public function testMultiCallbackInvalidData(){
		$this->db->multiCallback(
			$this->SQL_RAW_ERROR, [], function(){
		});
	}

	/**
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage invalid callback
	 */
	public function testMultiCallbackInvalidCallback(){
		$this->db->multiCallback($this->SQL_RAW_ERROR, [[0]], '');
	}

	/**
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage sql error:
	 */
	public function testMultiCallbackSQLError(){
		$this->db->multiCallback($this->SQL_RAW_ERROR, [[0]], function(){
			return [];
		});
	}

	public function testCreateTable(){
		$this->assertTrue($this->db->raw($this->SQL_RAW_DROP));
		$this->assertTrue($this->db->raw($this->SQL_RAW_CREATE));
	}

	public function testTruncate(){
		$this->assertTrue($this->db->raw($this->SQL_RAW_TRUNCATE));
		$this->assertTrue($this->db->raw($this->SQL_RAW_SELECT_ALL));
	}

	protected function resultTest(){
		$DBResult         = $this->db->raw($this->SQL_RAW_SELECT_ALL, $this->SQL_INDEX_COL);
		$DBResultPrepared = $this->db->prepared($this->SQL_RAW_SELECT_ALL, [], $this->SQL_INDEX_COL); // coverage

		$this->assertEquals($DBResult, $DBResultPrepared);
		$this->assertCount(10, $DBResult);

		$DBResult->__each(
			function($row, $i){
				/** @var \chillerlan\Database\ResultRow $row */
				$this->assertEquals($row->{$this->SQL_INDEX_COL}, $i);
				$this->assertSame($row->{$this->SQL_INDEX_COL}('md5'), $row->{$this->SQL_DATA_COL}());

				$row->__each(
					function($v, $j) use ($row){
						$this->assertEquals($row->{$j}, $v);
						$this->assertEquals($row[$j], $v);
					}
				);
			}
		);

		$this->assertTrue($this->db->raw($this->SQL_RAW_TRUNCATE));
	}

	public function testRaw(){

		foreach(range(0, 9) as $k){
			// don't try this at home!
			$this->assertTrue($this->db->raw(sprintf($this->SQL_RAW_INSERT, $k, md5($k))));
		}

		$this->resultTest();
	}

	public function testPrepared(){

		foreach(range(0, 9) as $k){
			$this->assertTrue($this->db->prepared($this->SQL_PREPARED_INSERT, [$k, md5($k)]));
		}

		$this->resultTest();
	}

	public function testMulti(){

		$this->assertTrue($this->db->multi($this->SQL_PREPARED_INSERT, array_map(function($k){
			return [$k, md5($k)];
		}, range(0, 9))));

		$this->resultTest();
	}

	public function testMultiCallback(){

		$this->assertTrue($this->db->multiCallback($this->SQL_PREPARED_INSERT, range(0, 9), function($k){
			return [$k, md5($k)];
		}));

		$this->resultTest();
	}

	public function testCached(){

		$this->assertTrue($this->db->multiCallback($this->SQL_PREPARED_INSERT, range(0, 9), function($k){
			return [$k, md5($k)];
		}));

		$getCacheKey = $this->getMethod($this->db, 'cacheKey');

		$cacheKey = $getCacheKey->invokeArgs($this->db, [$this->SQL_RAW_SELECT_ALL, [], $this->SQL_INDEX_COL]);

		// uncached
		$this->assertFalse($this->cache->has($cacheKey));
		$this->db->rawCached($this->SQL_RAW_SELECT_ALL, $this->SQL_INDEX_COL, true, 2);

		// cached
		$this->assertTrue($this->cache->has($cacheKey));
		$this->db->rawCached($this->SQL_RAW_SELECT_ALL, $this->SQL_INDEX_COL, true, 2);

		sleep(3);
		$this->cache->clear();

		// prepared uncached
		$this->assertFalse($this->cache->has($cacheKey));
		$this->db->preparedCached($this->SQL_RAW_SELECT_ALL, [], $this->SQL_INDEX_COL, true, 2);

		// prepared cached
		$this->assertTrue($this->cache->has($cacheKey));
		$this->db->preparedCached($this->SQL_RAW_SELECT_ALL, [], $this->SQL_INDEX_COL, true, 2);

		sleep(3);
		$this->cache->clear();

		$this->assertFalse($this->cache->has($cacheKey));
		$this->assertTrue($this->db->raw($this->SQL_RAW_TRUNCATE));
	}

}
