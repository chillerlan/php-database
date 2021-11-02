<?php
/**
 * Class DriverTestAbstract
 *
 * @created      21.04.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\{DriverException, DriverInterface};
use chillerlan\DatabaseTest\DBTestAbstract;
use ReflectionClass;

abstract class DriverTestAbstract extends DBTestAbstract{

	protected string $driverFQCN;

	protected function setUp():void{
		parent::setUp();

		$this->driver = new $this->driverFQCN($this->options, $this->cache, $this->logger);
		$this->driver->connect();

		$this->reflection = new ReflectionClass($this->driverFQCN);
	}

	abstract public function testGetDBResource():void;

	public function testInstance():void{
		// make sure we're disconnected
		$this->driver->disconnect();

		// try to disconnect from a disconnected DB (should not throw any errors -> mysqli)
		$this::assertTrue($this->driver->disconnect());

		// reconnect
		$this->driver->connect();

		// connect while already connected (coverage)
		$this::assertSame($this->driver, $this->driver->connect());

		$this::assertInstanceOf(DriverInterface::class, $this->driver);
		$this::assertInstanceOf($this->driverFQCN, $this->driver);

		$info = [
			'driver' => (new ReflectionClass($this->driver))->getName(),
			'client' => $this->driver->getClientInfo(),
			'server' => $this->driver->getServerInfo(),
		];

		$this->logger->info('driver info', $info);

		// trigger the destructor (coverage)
		unset($this->driver);
	}

	public function testEscapeString():void{
		// https://xkcd.com/327/
		$this::assertSame(
			"x'526f6265727427293b2044524f50205441424c452053747564656e74733b202d2d'",
			$this->driver->escape("Robert'); DROP TABLE Students; --")
		);
	}

	public function testEscapeEmptyString():void{
		$this::assertSame("''", $this->driver->escape(''));
	}

	public function testEscapeNull():void{
		$this::assertSame('null', $this->driver->escape(null));
	}

	public function testEscapeBool():void{
		$this::assertSame(1, $this->driver->escape(true));
		$this::assertSame(0, $this->driver->escape(false));
	}

	public function testEscapeNumeric():void{
		$this::assertSame(1337, $this->driver->escape('1337'));
		$this::assertSame(13.37, $this->driver->escape('13.37'));
		$this::assertSame(1330000000.0, $this->driver->escape('133e7'));
	}

	public function testGetInfoDisconnectedMessage():void{
		$this::assertTrue($this->driver->disconnect());

		$msg = 'disconnected, no info available';

		$this::assertSame($msg, $this->driver->getClientInfo());
		$this::assertSame($msg, $this->driver->getServerInfo());
	}

	public function testRawEmptySQLException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('sql error: empty sql');

		$this->driver->raw('');
	}

	public function testRawSQLErrorException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('sql error:');

		$this->driver->raw('SELECT foo bar');
	}

	public function testPreparedEmptySQLException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('sql error: empty sql');

		$this->driver->prepared('');
	}

	public function testPreparedSQLErrorException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('sql error:');

		$this->driver->prepared('SELECT foo bar ???');
	}

	public function testMultiEmptySQLException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('sql error: empty sql');

		$this->driver->multi('', []);
	}

	public function testMultiSQLErrorException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('sql error:');

		$this->driver->multi('UPDATE foo bar ???', [[0]]);
	}

	public function testMultiInvalidDataException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('invalid data');


		$this->driver->multi('UPDATE foo bar ???', []);
	}

	public function testMultiCallbackEmptySQLException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('sql error: empty sql');

		$this->driver->multiCallback('', [], function(){});
	}

	public function testMultiCallbackInvalidCallbackException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('invalid callback');

		$this->driver->multiCallback('UPDATE foo bar ???', [[0]], [$this, 'foo']);
	}

	public function testMultiCallbackSQLErrorException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('sql error:');

		$this->driver->multiCallback('UPDATE foo bar ???', [[0]], function($r){ return $r; });
	}

	public function testMultiCallbackInvalidDataException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('invalid data');

		$this->driver->multiCallback('UPDATE foo bar ???', [], function($r){ return $r; });
	}

}
