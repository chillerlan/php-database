<?php
/**
 * Class ResultTest
 *
 * @filesource   ResultTest.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest;

use chillerlan\Database\Result;
use Iterator, ArrayAccess, Countable, stdClass;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase{

	/**
	 * @var \chillerlan\Database\Result
	 */
	protected $result;

	protected function setUp(){
		$this->result = new Result(null, 'UTF-8');

		foreach(range(0, 9) as $k){
			$this->result[] = ['id' => $k, 'hash' => md5($k)];
		}

		$this->result->rewind();
	}

	public function testInstance(){
		$this->assertInstanceOf(Result::class, $this->result);
		$this->assertInstanceOf(Iterator::class, $this->result);
		$this->assertInstanceOf(ArrayAccess::class, $this->result);
		$this->assertInstanceOf(Countable::class, $this->result);

		//coverage
		new Result(new Result);
		new Result(new stdClass);
	}

	/**
	 * @expectedException \chillerlan\Database\DatabaseException
	 * @expectedExceptionMessage invalid data
	 */
	public function testConstructInvalidData(){
		new Result('');
	}

	public function testRow(){

		$this->assertSame(10, $this->result->length);
		$this->assertSame(md5(0), $this->result[0]->hash);

		while($row = $this->result->current()){
			$this->assertNull($row->foo);
			$this->assertNull($row->foo());
			$this->assertTrue(isset($row['hash'], $row['id']));
			$this->assertSame($row->id('md5'), $row->hash());
			$this->assertSame(['id' => $row->id, 'hash' => $row->hash], $row->__toArray());
			$this->assertSame(['hash' => $row->hash], $row->__chunk(1)[1]);
			$this->assertSame($row->id, $this->result->key());
			$this->assertSame($row->hash, $row->id(function($v){
				return md5($v);
			}));

			$this->assertTrue($this->result->valid());
			unset($this->result[$row->id]);
			$this->assertFalse($this->result->valid());

			$this->result->next();
		}

	}

	public function testEach(){
		$this->result->__each(function($row, $i){
			/** @var \chillerlan\Database\ResultRow $row */
			$this->assertSame($row->id, $i);
			$this->assertSame(md5($row->id), $row->hash());

			$row->__each(function($v, $j) use ($row){
				$this->assertSame($row->{$j}, $v);
				$this->assertSame($row[$j], $v);
			});
		});
	}

	/**
	 * @expectedException \chillerlan\Traits\TraitException
	 * @expectedExceptionMessage invalid callback
	 */
	public function testEachInvalidCallback(){
		$this->result->__each('foo');
	}

	public function testMerge(){
		$r1 = new Result([['id' => 1]]);
		$this->assertSame(1, $r1[0]->id);

		$r2 = new Result([['id' => 2]]);
		$this->assertSame(2, $r2[0]->id);

		$r1->__merge($r2)->__reverse();

		$this->assertSame(2, $r1[0]->id);
		$this->assertSame(1, $r1[1]->id);
	}

	public function testToArray(){
		$r = new Result([['id' => 1], ['id' => 2]]);
		$this->assertSame([['id' => 1], ['id' => 2]], $r->__toArray());
		$this->assertSame([['id' => 1]], $r->__chunk(1)[0]);
	}
}
