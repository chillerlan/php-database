<?php
/**
 * Class ResultTest
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest;

use chillerlan\Database\{Result, ResultInterface, ResultRow};
use ArrayAccess, Countable, SeekableIterator;
use PHPUnit\Framework\TestCase;

use function hex2bin, json_encode, md5, range;

class ResultTest extends TestCase{

	/**
	 * @var \chillerlan\Database\Result
	 */
	protected $result;

	protected function setUp():void{
		$this->result = new Result;

		foreach(range(0, 9) as $k){
			$this->result[] = ['id' => $k, 'hash' => md5($k)];
		}

		$this->result->rewind();
	}

	public function testInstance():void{
		$this::assertInstanceOf(ResultInterface::class, $this->result);
		$this::assertInstanceOf(ArrayAccess::class, $this->result);
		$this::assertInstanceOf(Countable::class, $this->result);
		$this::assertInstanceOf(SeekableIterator::class, $this->result);
	}

	public function testRow():void{
		$this::assertSame(10, $this->result->count());
		$this::assertSame(md5(0), $this->result[0]->hash);
		$this::assertSame(range(0, 9), $this->result->column('id'));


		/** @var \chillerlan\Database\ResultInterface|mixed $row */
		while($row = $this->result->current()){
			$this::assertSame(['id', 'hash'], $row->fields());
			$this::assertSame([$row->id, $row->hash], $row->values());
			$this::assertNull($row->foo);
			$this::assertNull($row->foo());
			$this::assertTrue(isset($row['hash'], $row['id']));
			$this::assertSame($row->id('md5'), $row->hash());
			$this::assertSame(['id' => $row->id, 'hash' => $row->hash], $row->toArray());
			$this::assertSame(['hash' => $row->hash], $row->chunk(1)[1]);
			$this::assertSame($row->id, $this->result->key());
			$this::assertSame($row->hash, $row->id(function($v){
				return md5($v);
			}));

			$this::assertTrue($this->result->valid());
			unset($this->result[$row->id]);
			$this::assertFalse($this->result->valid());


			$this->result->next();
		}

	}

	public function testEach():void{
		$this->result->each(function($row, $i){
			/** @var \chillerlan\Database\ResultRow|mixed $row */
			$this::assertSame($row->id, $i);
			$this::assertSame(md5($row->id), $row->hash());

			$row->each(function($v, $j) use ($row){
				$this::assertSame($row->{$j}, $v);
				$this::assertSame($row[$j], $v);
			});
		});
	}

	public function testMerge():void{
		$r1 = new Result([['id' => 1]]);
		$this::assertSame(1, $r1[0]->id);

		$r2 = new Result([['id' => 2]]);
		$this::assertSame(2, $r2[0]->id);

		$r1->merge($r2)->reverse();

		$this::assertSame(2, $r1[0]->id);
		$this::assertSame(1, $r1[1]->id);
	}

	public function testToArray():void{
		$r = new Result([['id' => 1], ['id' => 2]]);
		$this::assertSame([['id' => 1], ['id' => 2]], $r->toArray());
		$this::assertSame([['id' => 1]], $r->chunk(1)[0]);
	}

	public function testToJSON():void{
		$this::assertSame('[{"id":1},{"id":2}]', json_encode((new Result([['id' => 1], ['id' => 2]]))));
	}

	public function testRowOffsetSet():void{
		$r = new ResultRow;

		$r['id'] = 'foo';
		$r[] = 'bar';

		$this::assertSame('foo', $r['id']);
		$this::assertSame('bar', $r[0]);
	}

	public function testConvertEncoding():void{
		$r = new ResultRow(['name_zh' => hex2bin('72ee5b5062f195e8')], 'UTF-16', 'UTF-8');

		$this::assertSame('狮子拱门', $r->name_zh);
	}

}
