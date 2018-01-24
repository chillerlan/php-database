<?php
/**
 * Class DatabaseTest
 *
 * @filesource   DatabaseTest.php
 * @created      13.01.2018
 * @package      chillerlan\DatabaseTest
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest;

use chillerlan\Database\{
	Database, DatabaseOptions
};
use chillerlan\Database\Dialects\{
	Dialect, Firebird, MSSQL, MySQL, Postgres, SQLite
};
use chillerlan\Database\Drivers\{
	DriverInterface, FirebirdPDO, MySQLiDrv, MySQLPDO, PostgreSQL, PostgreSQLPDO, SQLitePDO
};
use chillerlan\Database\Query\QueryBuilder;
use chillerlan\Logger\{
	Log, LogOptions, LogTrait, Output\LogOutputAbstract
};
use chillerlan\SimpleCache\{
	Cache, Drivers\MemoryCacheDriver
};
use chillerlan\Traits\DotEnv;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use ReflectionClass, ReflectionMethod;

class DatabaseTest extends TestCase{

	use LogTrait;

	const TABLE = 'querytest';

	protected const DRIVERS = [
		// [driver, env_prefix, skip_on_ci]
		'MySQLiDrv'     => [MySQLiDrv::class, 'DB_MYSQLI', false],
		'MySQLPDO'      => [MySQLPDO::class, 'DB_MYSQLI', false],
		'PostgreSQL'    => [PostgreSQL::class, 'DB_POSTGRES', false],
		'PostgreSQLPDO' => [PostgreSQLPDO::class, 'DB_POSTGRES', false],
		'SQLitePDO'     => [SQLitePDO::class, 'DB_SQLITE3', false],
		'SQLitePDOMem'  => [SQLitePDO::class, 'SQLITE_MEM', false],
		'FirebirdPDO'   => [FirebirdPDO::class, 'DB_FIREBIRD', true],
#		'MSSqlSrv'      => [MSSqlSrv::class, 'DB_MSSQL', true],
#		'MSSqlSrvPDO'   => [MSSqlSrvPDO::class, 'DB_MSSQL', true],
		];

	/**
	 * @var \chillerlan\Database\DatabaseOptions
	 */
	protected $options;

	/**
	 * @var \Psr\SimpleCache\CacheInterface
	 */
	protected $cache;

	/**
	 * @var \chillerlan\Database\Database
	 */
	public $db;

	/**
	 * @var \chillerlan\Database\Drivers\DriverInterface
	 */
	protected $driver;

	/**
	 * @var \chillerlan\Database\Query\QueryBuilder
	 */
	protected $query;

	/**
	 * @var \chillerlan\Database\Dialects\Dialect
	 */
	protected $dialect;

	/**
	 * @var \chillerlan\Traits\DotEnv
	 */
	protected $env;

	/**
	 * determines whether the tests run on Travis CI or not -> .env IS_CI=TRUE
	 *
	 * @var bool
	 */
	protected $isCI;

	/**
	 *
	 */
	protected function setUp(){
		$this->env = (new DotEnv(__DIR__.'/../config', file_exists(__DIR__.'/../config/.env') ? '.env' : '.env_travis'))->load();

		$this->isCI = $this->env->get('IS_CI') === 'TRUE';


		$logger = new Log;

		// no log spam on travis
		if(!$this->isCI){

			$logger->addInstance(
				new class (new LogOptions(['minLogLevel' => LogLevel::DEBUG])) extends LogOutputAbstract{

					protected function __log(string $level, string $message, array $context = null):void{
						echo $message.PHP_EOL.print_r($context, true).PHP_EOL;
					}

				},
				'console'
			);

		}

		$this->setLogger($logger);
	}

	/**
	 *
	 */
	protected function tearDown(){

		if(isset($this->db) && $this->db instanceof Database){
			$this->assertTrue($this->db->disconnect());
		}

	}

	/**
	 * @param $class
	 * @param $name
	 *
	 * @return \ReflectionMethod
	 */
	protected function getMethod($class, $name):ReflectionMethod{
		$method = (new ReflectionClass($class))->getMethod($name);
		$method->setAccessible(true);

		return $method;
	}

	/**
	 * @param string $driver
	 * @param string $env_prefix
	 * @param bool   $skip_on_ci
	 * @param array  $options
	 * @param bool   $cached
	 *
	 * @return $this
	 */
	protected function dbInstance(string $driver, string $env_prefix, bool $skip_on_ci, array $options = [], bool $cached = false){

		if($skip_on_ci === true && $this->isCI){
			$this->markTestSkipped('test on Vagrant/local: '.$driver);

			return $this;
		}

		if(isset($this->db) && $this->db instanceof Database){
			$this->db->disconnect();
		}

		$this->options = new DatabaseOptions(
			array_merge(
				[
					'driver'   => $driver,
					'host'     => $this->env->get($env_prefix.'_HOST'),
					'port'     => $this->env->get($env_prefix.'_PORT'),
					'socket'   => $this->env->get($env_prefix.'_SOCKET'),
					'database' => $this->env->get($env_prefix.'_DATABASE'),
					'username' => $this->env->get($env_prefix.'_USERNAME'),
					'password' => $this->env->get($env_prefix.'_PASSWORD'),
				], $options
			)
		);

		$this->cache  = new Cache(new MemoryCacheDriver);
		$this->db     = new Database($this->options, $cached ? $this->cache : null, $this->log);
		$this->driver = $this->db->getDriver();

		$this->assertSame($this->db, $this->db->connect());

		$this->query   = $this->db->getQueryBuilder();
		$this->dialect = $this->db->getDialect();

		return $this;
	}

	/**
	 *
	 */
	protected function createTable(){
		$this->assertTrue(
			$this->db->create
				->table($this::TABLE)
				->ifNotExists()
				->primaryKey('id')
	    		->charset('utf8')
				->int('id', 10)
				->varchar('hash', 32)
				->text('data', null, null)
				->field('value', 'decimal', '9,6')
				->field('active', 'boolean', null, null, null, null, null, 'false')
				->field('created', 'timestamp', null, null, null, null, 'CURRENT_TIMESTAMP')
				->query()
		);

		$this->assertTrue($this->db->truncate->table($this::TABLE)->query());
	}

	/**
	 * @return array
	 */
	protected function data():array{
		return [
			['id' => 1, 'hash' => md5(1), 'data' => 'foo', 'value' => 123.456, 'active' => 0],
			['id' => 2, 'hash' => md5(2), 'data' => 'bar', 'value' => 123.456789, 'active' => 1],
			['id' => 3, 'hash' => md5(3), 'data' => 'baz', 'value' => 123.456, 'active' => 0],
		];
	}

	/**
	 * @return array
	 */
	public function driverProvider(){
		return $this::DRIVERS;
	}

	/**
	 * @dataProvider driverProvider
	 */
	public function testConnectDisconnect(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);

		$this->assertTrue($this->db->disconnect());
		// try to disconnect from a disconnected DB (should not throw any errors -> mysqli)
		$this->assertTrue($this->db->disconnect());

		// reconnect
		$this->driver = $this->db->connect();
		$this->assertInstanceOf(DriverInterface::class, $this->driver);

		// connect while already connected (coverage)
		$this->assertSame($this->driver, $this->db->connect());

		// trigger the destructor (coverage)
		unset($this->db);
	}

	/**
	 * @dataProvider driverProvider
	 */
	public function testInstance(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);

		$this->assertInstanceOf(Database::class, $this->db);
		$this->assertInstanceOf(DriverInterface::class, $this->db);

		$this->assertInstanceOf($driver, $this->driver);
		$this->assertInstanceOf(DriverInterface::class, $this->driver);

		$this->assertInstanceOf(QueryBuilder::class, $this->query);
		$this->assertInstanceOf(Dialect::class, $this->dialect);

		$info = [
			'driver' => $driver,
			'client' => $this->db->getClientInfo(),
			'server' => $this->db->getServerInfo(),
		];

		$this->log->info(print_r($info, true));
	}

	/**
	 * @dataProvider driverProvider
	 * @todo         break things
	 */
	public function testEscapeString(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);

		$this->db->create
			->table('Students')
			->varchar('name', 40)
			->query()
		;

		$str = "Robert'); DROP TABLE Students; --";
		$str = $this->db->escape($str); // comment this line for fancy!

		if($this->dialect instanceof MySQL){
			$this->assertSame("'Robert\'); DROP TABLE Students; --'", $str);
		}
		elseif($this->dialect instanceof Postgres){
			$this->assertSame("'Robert''); DROP TABLE Students; --'", $str);
		}
		elseif($this->dialect instanceof SQLite){
			$this->assertSame("'Robert''); DROP TABLE Students; --'", $str);
		}
		elseif($this->dialect instanceof Firebird){
			$this->assertSame("'Robert''); DROP TABLE Students; --'", $str);
		}
		elseif($this->dialect instanceof MSSQL){
#			$this->assertSame("'Robert''); DROP TABLE Students; --'", $str);
		}
		else{
			$this->markTestSkipped('https://xkcd.com/327/');
		}

		$this->assertTrue($this->db->raw('INSERT INTO '.$this->dialect->quote('Students').' VALUES ('.$str.')'));
		$this->assertSame("Robert'); DROP TABLE Students; --", $this->db->select->from(['Students'])->query()[0]->name);
		$this->assertTrue($this->db->drop->table('Students')->query());
	}

	/**
	 * @dataProvider driverProvider
	 */
	public function testInsert(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci)->createTable();

		$this->assertTrue(
			$this->db->insert
				->into($this::TABLE)
				->values(['id' => 0, 'hash' => md5(0), 'data' => 'foo', 'value' => 123.456, 'active' => 1])
				->query()
		);

		$r = $this->db->select->from([$this::TABLE])->query()[0];
		$this->assertSame(0, (int)$r->id); // @todo: SQLite types
		$this->assertSame('foo', $r->data);
		$this->assertSame(123.456, (float)$r->value);
		$this->assertTrue((bool)$r->active);
	}

	/**
	 * @dataProvider driverProvider
	 */
	public function testInsertMulti(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci)->createTable();

		$this->assertTrue(
			$this->db->insert
				->into($this::TABLE, 'IGNORE')
				->values($this->data())
				->multi()
		);

		$r = $this->db->select->from([$this::TABLE])->query();
		$this->assertSame(3, $r->length);
		$this->assertSame(123.456789, (float)$r[1]->value);
		$this->assertSame(3, (int)$r[2]->id);
	}

	/**
	 * @dataProvider driverProvider
	 */
	public function testSelect(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci)->createTable();
		$this->db->insert->into($this::TABLE)->values($this->data())->multi();

		$q = $this->db->select
			->cols(['id' => 't1.id', 'hash' => ['t1.hash']])
			->from(['t1' => $this::TABLE])
			->offset(1)
			->limit(2)
		;

		$this->assertSame(3, $q->count()); // ignores limit/offset

		$r = $q->query();

		$this->assertSame(2, $r->length);
		$this->assertSame(2, (int)$r[0]['id']);
		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame(md5(3), $r[1]->id('md5'));
		$this->assertSame(md5(2), $r[0]['hash']);
		$this->assertSame(md5(3), $r[1]->hash);

		$r = $this->db->select
			->cols(['hash', 'value'])
			->from([$this::TABLE])
			->where('active', 1)
			->query('hash')
		;

		$this->assertSame('{"c81e728d9d4c2f636f067f89cc14862c":{"hash":"c81e728d9d4c2f636f067f89cc14862c","value":"123.456789"}}', $r->__toJSON());

		$r = $this->db->select
			->from([$this::TABLE])
			->where('id', [1, 2], 'in')
			->orderBy(['hash' => 'desc'])
			->query()
		;

		$this->assertSame('bar', $r[0]->data);
		$this->assertSame('foo', $r[1]->data);
		$this->assertTrue((bool)$r[0]->active);
		$this->assertFalse((bool)$r[1]->active);
	}

	/**
	 * @dataProvider driverProvider
	 */
	public function testUpdate(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci)->createTable();
		$this->db->insert->into($this::TABLE)->values($this->data())->multi();

		$this->assertTrue(
			$this->query->update
				->table($this::TABLE)
				->set(
					[
						'data'   => 'whatever',
						'value'  => 42.42,
						'active' => 1,
					]
				)
				->where('id', 1)
				->query()
		);

		$r = $this->query->select
			     ->cols(['hash', 'data', 'value', 'active'])
			     ->from([$this::TABLE])
			     ->where('id', 1)
			     ->query('hash')
		['c4ca4238a0b923820dcc509a6f75849b'];

		$this->assertSame('whatever', $r->data);
		$this->assertSame(42.42, (float)$r->value);
		$this->assertTrue((bool)$r->active);
	}

	/**
	 * @dataProvider driverProvider
	 */
	public function testDelete(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci)->createTable();
		$this->db->insert->into($this::TABLE)->values($this->data())->multi();

		$q = $this->query->select->cols(['hash'])->from([$this::TABLE]);

		$this->assertSame(
			[
				'c4ca4238a0b923820dcc509a6f75849b', 'c81e728d9d4c2f636f067f89cc14862c', 'eccbc87e4b5ce2fe28308fd9f2a7baf3'
			], array_column($q->query()->__toArray(), 'hash')
		);

		$this->assertTrue($this->query->delete->from($this::TABLE)->where('id', 2)->query());

		$r = $q->query();

		$this->assertSame(2, $r->length);
		$this->assertSame(
			[
				'c4ca4238a0b923820dcc509a6f75849b', 'eccbc87e4b5ce2fe28308fd9f2a7baf3'
			], array_column($r->__toArray(), 'hash')
		);
	}

	/**
	 * @dataProvider driverProvider
	 */
	public function testSelectCached(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci, [], true)->createTable();

		if($this->isCI && $this->dialect instanceof Postgres){
			$this->markTestSkipped('sup postgres?');
			return;
		}

		$this->db->insert
			->into($this::TABLE)
			->values([['id' => '?', 'hash' => '?']])
			->callback(range(1, 10), function($k){
				return [$k, md5($k)];
			})
		;

		$r           = $this->db->select->from([$this::TABLE])->cached(2);
		$getCacheKey = $this->getMethod($this->driver, 'cacheKey');

		$cacheKey = $getCacheKey->invokeArgs($this->driver, [$r->sql(), [], 'hash']);

		// uncached
		$this->assertFalse($this->cache->has($cacheKey));
		$r->query('hash');

		// cached
		$this->assertTrue($this->cache->has($cacheKey));
		$r->query('hash');

		sleep(2);
#		$this->cache->clear();

		// raw uncached
		$this->assertFalse($this->cache->has($cacheKey));
		$this->db->rawCached($r->sql(), 'hash', true, 1);

		// cached
		$this->assertTrue($this->cache->has($cacheKey));
		$this->db->rawCached($r->sql(), 'hash', true, 1);

		sleep(2);
#		$this->cache->clear();

		// prepared uncached
		$this->assertFalse($this->cache->has($cacheKey));
		$this->db->preparedCached($r->sql(), [], 'hash', true, 1);

		// cached
		$this->assertTrue($this->cache->has($cacheKey));
		$this->db->preparedCached($r->sql(), [], 'hash', true, 1);
	}

	/**
	 * @dataProvider driverProvider
	 */
	public function testShowDatabases(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);

		if($this->dialect instanceof SQLite){
			$this->markTestSkipped('not supported');

			return;
		}

		$r = $this->db->show->databases()->query()->__toArray();
		$this->debug('SHOW DATABASES:', $r);

		$this->assertTrue(in_array($this->env->get($env_prefix.'_DATABASE'), array_column($r, 'Database')));
	}

	/**
	 * @dataProvider driverProvider
	 */
	public function testShowTables(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);

		if($this->dialect instanceof SQLite && $this->env->get($env_prefix.'_DATABASE') === ':memory:'){
			$this->markTestSkipped('not supported');

			return;
		}

		$r = $this->db->show->tables()->query()->__toArray();

		$this->debug('SHOW TABLES:', $r);

		foreach($r as $tables){
			[$table] = array_values($tables);

			if($table === $this::TABLE){
				$this->assertSame($this::TABLE, $table);
				break;
			}

		}

	}

	/**
	 * @dataProvider driverProvider
	 */
	public function testShowCreateTable(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci)->createTable();

		if($this->dialect instanceof SQLite && $this->env->get($env_prefix.'_DATABASE') === ':memory:'){
			$this->markTestSkipped('not supported');

			return;
		}

		$r = $this->db->show->create->table($this::TABLE)->query();

		$this->assertTrue($this->db->drop->table($this::TABLE)->query());
		$this->assertTrue($this->db->prepared($r[0]->{'Create Table'}));
	}

	// exceptions galore!

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage db error:
	 */
	public function testConnectError(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci, ['host' => '...', 'database' => '...']);
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage invalid statement
	 */
	public function testInvalidStatement(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);
		$this->db->foo;
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no name specified
	 */
	public function testCreateDatabaseNoName(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);
		$this->db->create->database('')->sql();
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no name specified
	 */
	public function testCreateTableNoName(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);
		$this->db->create->table('')->sql();
	}

	/**
	 * @dataProvider driverProvider
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no name specified
	 */
	public function testDropDatabaseNoName(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);
		$this->db->drop->database('')->sql();
	}

	/**
	 * @dataProvider driverProvider
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no name specified
	 */
	public function testDropTableNoName(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);
		$this->db->drop->table('')->sql();
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no name specified
	 */
	public function testInsertNoTable(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);
		$this->db->insert->into('')->sql();
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no values given
	 */
	public function testInsertInvalidData(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);
		$this->db->insert->into('foo')->values([])->sql();
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no FROM expression specified
	 */
	public function testSelectEmptyFrom(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);
		$this->db->select->from([])->sql();
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no name specified
	 */
	public function testUpdateNoTable(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);
		$this->db->update->table('')->sql();
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no fields to update specified
	 */
	public function testUpdateNoSet(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);
		$this->db->update->table('foo')->set([])->sql();
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Query\QueryException
	 * @expectedExceptionMessage no name specified
	 */
	public function testDeleteNoTable(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);
		$this->db->delete->from('')->sql();
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage sql error: empty sql
	 */
	public function testRawEmptySQL(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);

		$this->db->raw('');
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage sql error:
	 */
	public function testRawSQLError(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);

		$this->db->raw('SELECT foo bar');
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage sql error: empty sql
	 */
	public function testPreparedEmptySQL(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);

		$this->db->prepared('');
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage sql error:
	 */
	public function testPreparedSQLError(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);

		$this->db->prepared('SELECT foo bar ???');
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage sql error: empty sql
	 */
	public function testMultiEmptySQL(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);

		$this->db->multi('', []);
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage sql error:
	 */
	public function testMultiSQLError(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);

		$this->db->multi('UPDATE foo bar ???', [[0]]);
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage invalid data
	 */
	public function testMultiInvalidData(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);

		$this->db->multi('UPDATE foo bar ???', []);
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage sql error: empty sql
	 */
	public function testMultiCallbackEmptySQL(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);

		$this->db->multiCallback('', [], function(){});
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage invalid callback
	 */
	public function testMultiCallbackInvalidCallback(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);

		$this->db->multiCallback('UPDATE foo bar ???', [[0]], [$this, 'foo']);
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage sql error:
	 */
	public function testMultiCallbackSQLError(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);

		$this->db->multiCallback('UPDATE foo bar ???', [[0]], function($r){ return $r; });
	}

	/**
	 * @dataProvider             driverProvider
	 * @expectedException \chillerlan\Database\Drivers\DriverException
	 * @expectedExceptionMessage invalid data
	 */
	public function testMultiCallbackInvalidData(string $driver, string $env_prefix, bool $skip_on_ci){
		$this->dbInstance($driver, $env_prefix, $skip_on_ci);

		$this->db->multiCallback('UPDATE foo bar ???', [], function($r){ return $r; });
	}

}
