<?php
/**
 * Class QueryTestAbstract
 *
 * @created      02.11.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\DatabaseTest\Query;

use chillerlan\Database\Dialects\Dialect;
use chillerlan\Database\Drivers\DriverInterface;
use chillerlan\Database\Query\QueryException;
use chillerlan\Database\ResultInterface;
use chillerlan\DatabaseTest\DBTestAbstract;
use ReflectionClass;

use function array_column, array_values, in_array, json_encode, md5, range, sleep;

abstract class QueryTestAbstract extends DBTestAbstract{

	const TABLE = 'querytest';

	protected function setUp():void{
		parent::setUp();

		$r = $this->db->create
			->table($this::TABLE)
			->ifNotExists()
			->primaryKey('id')
			->charset('utf8')
			->int('id', 10)
			->varchar('hash', 32)
			->text('data', null, true)
			->field('value', 'decimal', '9,6', null, null, true)
			->field('active', 'boolean', null, null, null, null, null, 'false')
			->field('created', 'timestamp', null, null, null, null, 'CURRENT_TIMESTAMP')
			->executeQuery();

		$this::assertTrue($r->isSuccess());
	}

	protected function tearDown():void{
		$r = $this->db->drop->table($this::TABLE)->ifExists()->executeQuery();

		$this::assertTrue($r->isSuccess());
		$this::assertTrue($this->db->disconnect());

		parent::tearDown();
	}

	// the following 2 tests don't use type casting to make sure we receive the "correct" types from the underlying driver

	abstract protected function assertInsertResult(ResultInterface $result):void;
	abstract protected function assertInsertMultiResult(ResultInterface $result):void;

	protected function data():array{
		return [
			['id' => 1, 'hash' => md5('1'), 'data' => 'foo', 'value' => 123.456, 'active' => 0],
			['id' => 2, 'hash' => md5('2'), 'data' => 'bar', 'value' => 123.456789, 'active' => 1],
			['id' => 3, 'hash' => md5('3'), 'data' => 'baz', 'value' => 123.456, 'active' => 0],
		];
	}

	public function testInsert():void{

		$insert = $this->db->insert
			->into($this::TABLE)
			->values(['id' => 0, 'hash' => md5('0'), 'data' => 'foo', 'value' => 123.456, 'active' => 1])
			->executeQuery();

		$this::assertTrue($insert->isSuccess());

		$this::assertInsertResult($this->db->select->from([$this::TABLE])->executeQuery());
	}

	public function testInsertMulti():void{

		$insert = $this->db->insert
			->into($this::TABLE, 'IGNORE', 'id')
			->values($this->data())
			->executeMultiQuery();

		$this::assertTrue($insert);

		$this::assertInsertMultiResult($this->db->select->from([$this::TABLE])->executeQuery());
	}

	public function testSelect():void{

		$this->db->insert
			->into($this::TABLE)
			->values($this->data())
			->executeMultiQuery();

		$q = $this->db->select
			->cols(['id' => 't1.id', 'hash' => ['t1.hash']])
			->from(['t1' => $this::TABLE])
			->offset(1)
			->limit(2)
		;

		$this::assertSame(3, $q->count()); // ignores limit/offset

		$r = $q->executeQuery();

		$this::assertSame(2, $r->count());
		$this::assertSame(2, (int)$r[0]['id']);
		$this::assertSame(md5('2'), $r[0]['hash']);
		$this::assertSame(md5('3'), $r[1]->id(fn(int $v):string => md5((string)$v)));
		$this::assertSame(md5('3'), $r[1]->hash);

		$r = $this->db->select
			->cols(['hash', 'value'])
			->from([$this::TABLE])
			->where('active', 1)
			->executeQuery('hash')
		;

		$this::assertSame(
			'{"c81e728d9d4c2f636f067f89cc14862c":{"hash":"c81e728d9d4c2f636f067f89cc14862c","value":"123.456789"}}',
			json_encode($r)
		);

		$r = $this->db->select
			->from([$this::TABLE])
			->where('id', [1, 2], 'in')
			->orderBy(['hash' => 'desc'])
			->executeQuery()
		;

		$this::assertSame('bar', $r[0]->data);
		$this::assertSame('foo', $r[1]->data);
		$this::assertTrue((bool)$r[0]->active);
		$this::assertFalse((bool)$r[1]->active);
	}

	public function testUpdate():void{

		$this->db->insert
			->into($this::TABLE)
			->values($this->data())
			->executeMultiQuery();

		$update = $this->db->update
			->table($this::TABLE)
			->set([
				'data'   => 'whatever',
				'value'  => 42.42,
				'active' => 1,
			])
			->where('id', 1)
			->executeQuery();

		$this::assertTrue($update->isSuccess());

		$result = $this->db->select
			->cols(['hash', 'data', 'value', 'active'])
			->from([$this::TABLE])
			->where('id', 1)
			->executeQuery('hash');

		$this::assertTrue($result->isSuccess());

		$r = $result['c4ca4238a0b923820dcc509a6f75849b'];

		$this::assertSame('whatever', $r->data);
		$this::assertSame(42.42, (float)$r->value);
		$this::assertTrue((bool)$r->active);
	}

	public function testDelete():void{

		$this->db->insert
			->into($this::TABLE)
			->values($this->data())
			->executeMultiQuery();

		$q = $this->db->select->cols(['hash'])->from([$this::TABLE]);

		$this::assertSame([
			'c4ca4238a0b923820dcc509a6f75849b',
			'c81e728d9d4c2f636f067f89cc14862c',
			'eccbc87e4b5ce2fe28308fd9f2a7baf3',
		], array_column($q->executeQuery()->toArray(), 'hash'));

		$delete = $this->db->delete
			->from($this::TABLE)
			->where('id', 2)
			->executeQuery();

		$this::assertTrue($delete->isSuccess());

		$r = $q->executeQuery();

		$this::assertSame(2, $r->count());
		$this::assertSame([
			'c4ca4238a0b923820dcc509a6f75849b',
			'eccbc87e4b5ce2fe28308fd9f2a7baf3',
		], array_column($r->toArray(), 'hash'));
	}

	public function testSelectCached():void{

		$this->db->insert
			->into($this::TABLE)
			->values([['id' => '?', 'hash' => '?']])
			->executeMultiQuery(range(1, 10), fn($k) => [$k, md5((string)$k)])
		;

		$r           = $this->db->select->from([$this::TABLE])->cached(2);
		$getCacheKey = $this->getMethod('cacheKey');
		$cacheKey    = $getCacheKey->invokeArgs($this->db, [$r->getSQL(), [], 'hash']);

		// uncached
		$this::assertFalse($this->cache->has($cacheKey));
		$r->executeQuery('hash');

		// cached
		$this::assertTrue($this->cache->has($cacheKey));
		$r->executeQuery('hash');

		sleep(2);
#		$this->cache->clear();

		// raw uncached
		$this::assertFalse($this->cache->has($cacheKey));
		$this->db->rawCached($r->getSQL(), 'hash', true, 1);

		// cached
		$this::assertTrue($this->cache->has($cacheKey));
		$this->db->rawCached($r->getSQL(), 'hash', true, 1);

		sleep(2);
#		$this->cache->clear();

		// prepared uncached
		$this::assertFalse($this->cache->has($cacheKey));
		$this->db->preparedCached($r->getSQL(), [], 'hash', true, 1);

		// cached
		$this::assertTrue($this->cache->has($cacheKey));
		$this->db->preparedCached($r->getSQL(), [], 'hash', true, 1);
	}

	public function testShowDatabases():void{

		$r = $this->db->show
			->databases()
			->executeQuery()
			->toArray();

		$this->logger->debug('SHOW DATABASES:', $r);

		$this::assertTrue(in_array($this->env->get($this->envPrefix.'_DATABASE'), array_column($r, 'Database')));
	}

	public function testShowTables():void{

		$r = $this->db->show
			->tables()
			->executeQuery()
			->toArray();

		$this->logger->debug('SHOW TABLES:', $r);

		foreach($r as $tables){
			[$table] = array_values($tables);

			if($table === $this::TABLE){
				$this::assertSame($this::TABLE, $table);
				break;
			}

		}

	}


	// exceptions galore!

	public function testInvalidStatementException():void{
		$this->expectException(QueryException::class);
		$this->expectExceptionMessage('invalid statement');

		/** @noinspection PhpExpressionResultUnusedInspection */
		$this->db->foo;
	}

	public function testCreateDatabaseNoNameException():void{
		$this->expectException(QueryException::class);
		$this->expectExceptionMessage('no name specified');

		$this->db->create->database('')->getSQL();
	}

	public function testCreateTableNoNameException():void{
		$this->expectException(QueryException::class);
		$this->expectExceptionMessage('no name specified');

		$this->db->create->table('')->getSQL();
	}

	public function testDropDatabaseNoNameException():void{
		$this->expectException(QueryException::class);
		$this->expectExceptionMessage('no name specified');

		$this->db->drop->database('')->getSQL();
	}

	public function testDropTableNoNameException():void{
		$this->expectException(QueryException::class);
		$this->expectExceptionMessage('no name specified');

		$this->db->drop->table('')->getSQL();
	}

	public function testInsertTableNoNameException():void{
		$this->expectException(QueryException::class);
		$this->expectExceptionMessage('no name specified');

		$this->db->insert->into('')->getSQL();
	}

	public function testInsertInvalidDataException():void{
		$this->expectException(QueryException::class);
		$this->expectExceptionMessage('no values given');

		$this->db->insert->into('foo')->values([])->getSQL();
	}

	public function testSelectEmptyFromException():void{
		$this->expectException(QueryException::class);
		$this->expectExceptionMessage('no FROM expression specified');

		$this->db->select->from([])->getSQL();
	}

	public function testUpdateNoTableException():void{
		$this->expectException(QueryException::class);
		$this->expectExceptionMessage('no name specified');

		$this->db->update->table('')->getSQL();
	}

	public function testUpdateNoSetException():void{
		$this->expectException(QueryException::class);
		$this->expectExceptionMessage('no fields to update specified');

		$this->db->update->table('foo')->set([])->getSQL();
	}

	public function testDeleteNoTableException():void{
		$this->expectException(QueryException::class);
		$this->expectExceptionMessage('no name specified');

		$this->db->delete->from('')->getSQL();
	}

}
