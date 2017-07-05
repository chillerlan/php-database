<?php
/**
 * Class QueryTestAbstract
 *
 * @filesource   QueryTestAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\DatabaseTest\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query;

use chillerlan\Database\Query\Statements\{
	CreateDatabase, CreateTable, Delete, Insert,
	Select, Statement, Update
};

use chillerlan\Database\Drivers\DriverException;
use chillerlan\Database\Query\QueryBuilderInterface;
use chillerlan\Database\Result;
use chillerlan\DatabaseTest\ConnectionTestAbstract;

abstract class QueryTestAbstract extends ConnectionTestAbstract{

	const TEST_DBNAME = 'vagrant';
	const TEST_TABLENAME = 'querytest';

	/**
	 * @var \chillerlan\Database\Query\QueryBuilderInterface
	 */
	protected $query;

	protected function setUp(){
		parent::setUp();

		$this->query = $this->connection->getQueryBuilder();
	}

	public function testQueryBuilderInstance(){
		$this->assertInstanceOf($this->querydriver, $this->query);
		$this->assertInstanceOf(QueryBuilderInterface::class, $this->query);
	}

	protected function createDatabase(){
		$createdb = $this->query->create->database(self::TEST_DBNAME);
		$this->assertInstanceOf(Statement::class, $createdb);
		$this->assertInstanceOf(CreateDatabase::class, $createdb);

		return $createdb->name(self::TEST_DBNAME)->charset('utf8');
	}

	/**
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no name specified
	 */
	public function testCreateDatabaseNoName(){
		$this->query->create->database()->sql();
	}

	public function testCreateTable(){

		try{
			$this->connection->raw('DROP TABLE '.self::TEST_TABLENAME);
		}
		catch(DriverException $e){
			var_dump('cannot drop "'.self::TEST_TABLENAME.'", table does not exist');
		}

		$createTable = $this->query->create->table(self::TEST_DBNAME.'.'.self::TEST_TABLENAME);

		$this->assertInstanceOf(Statement::class, $createTable);
		$this->assertInstanceOf(CreateTable::class, $createTable);

		$createTable->name(self::TEST_TABLENAME)
			->ifNotExists()
#			->temp()
			->charset('utf8')
			->field('id', 'int', 10)
			->field('hash', 'varchar', 32)
			->field('data', 'text', null, null, 'utf8')
			->field('value', 'decimal', '9,6')
			->field('active', 'boolean', null, null, null, null, null, 'false')
			->field('created', 'timestamp', null, null, null, null, 'CURRENT_TIMESTAMP')
			->primaryKey('id')
		;

#		print_r(PHP_EOL.$createTable->sql().PHP_EOL);

		$this->assertTrue($createTable->execute());
	}

	/**
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no name specified
	 */
	public function testCreateTableNoName(){
		$this->query->create->table()->sql();
	}

	public function testInsert(){
		$insert = $this->query->insert;

		$this->assertInstanceOf(Statement::class, $insert);
		$this->assertInstanceOf(Insert::class, $insert);

		$insert
			->into(self::TEST_TABLENAME)
			->values(['id' => 0, 'hash' => md5(0), 'data' => 'foo', 'value' => 123.456, 'active' => 1])
		;

#		print_r(PHP_EOL.$insert->sql().PHP_EOL);

		$this->assertTrue($insert->execute());
	}

	public function testInsertMulti(){
		$insert = $this->query->insert;

		$insert
			->into(self::TEST_TABLENAME)
			->values([
				['id' => 1, 'hash' => md5(1), 'data' => 'foo', 'value' => 123.456,    'active' => 0],
				['id' => 2, 'hash' => md5(2), 'data' => 'foo', 'value' => 123.456789, 'active' => 1],
				['id' => 3, 'hash' => md5(3), 'data' => 'foo', 'value' => 123.456,    'active' => 0],
			])
		;

		$this->assertTrue($insert->execute());
	}

	/**
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no table specified
	 */
	public function testInsertNoTable(){
		$this->query->insert->into('')->sql();
	}

	/**
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no values given
	 */
	public function testInsertInvalidData(){
		$this->query->insert->into(self::TEST_TABLENAME)->values([])->sql();
	}

	public function testSelect(){
		$select = $this->query->select;
		$this->assertInstanceOf(Statement::class, $select);
		$this->assertInstanceOf(Select::class, $select);

		$select
			->cols([
				'id'   => 't1.id',
				'hash' => ['t1.hash', 'upper'],
			])
			->from(['t1' => self::TEST_TABLENAME])
			->offset(1)
			->limit(2);
#		print_r(PHP_EOL.$select->sql().PHP_EOL);
		$result = $select->execute();
		$this->assertInstanceOf(Result::class, $result);
		$this->assertCount(2, $result);
#		print_r($result);

		$select = $this->query->select;
		$select
			->cols(['id', 'hash', 'value'])
			->from([self::TEST_TABLENAME])
			->where('active', 1);
#		print_r(PHP_EOL.$select->sql().PHP_EOL);
		$result = $select->execute();
		$this->assertInstanceOf(Result::class, $result);
		$this->assertCount(2, $result);
#		print_r($result);

		$select = $this->query->select;
		$select
			->from([self::TEST_TABLENAME])
			->where('id', [1,2,3], 'in')
			->orderBy(['hash' => ['desc', 'lower']]);
#		print_r(PHP_EOL.$select->sql().PHP_EOL);
		$result = $select->execute();
#		print_r($result);

		if((bool)$result[0]->active){
			// postgres t/f
			$this->markTestSkipped('['.get_called_class().'] invalid boolean value');
		}

		$this->assertCount(3, $result);
		$this->assertFalse((bool)$result[0]->active);
		$this->assertEquals(3, $result[0]->id); // sqlite will return the id as string, regardless
	}

	/**
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no FROM expression specified
	 */
	public function testSelectEmptyFrom(){
		$this->query->select->from([])->sql();
	}

	public function testUpdate(){
		$update = $this->query->update;
		$this->assertInstanceOf(Statement::class, $update);
		$this->assertInstanceOf(Update::class, $update);

		$update
			->table(self::TEST_TABLENAME)
			->set([
				'data'   => 'bar',
				'value'  => 42.42,
				'active' => 1,
			])
			->where('id', 1)
		;

#		print_r(PHP_EOL.$update->sql().PHP_EOL);
		$this->assertTrue($update->execute());

		$r = $this->query->select->from([self::TEST_TABLENAME])->where('id' ,1)->execute();

		$this->assertSame('bar', $r[0]->data);
		$this->assertSame(42.42, (float)$r[0]->value);
		$this->assertTrue((bool)$r[0]->active);
	}

	/**
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no table specified
	 */
	public function testUpdateNoTable(){
		$this->query->update->table('')->sql();
	}

	/**
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no fields to update specified
	 */
	public function testUpdateNoSet(){
		$this->query->update->table(self::TEST_TABLENAME)->set([])->sql();
	}

	public function testDelete(){
		$delete = $this->query->delete;
		$this->assertInstanceOf(Statement::class, $delete);
		$this->assertInstanceOf(Delete::class, $delete);

		$delete->from(self::TEST_TABLENAME)->where('id', 2);

#		print_r(PHP_EOL.$delete->sql().PHP_EOL);
		$this->assertTrue($delete->execute());

		$r = $this->query->select->from([self::TEST_TABLENAME])->execute();
		$this->assertCount(3,  $r);
	}

	/**
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no table specified
	 */
	public function testDeleteNoTable(){
		$this->query->delete->from('')->sql();
	}

}
