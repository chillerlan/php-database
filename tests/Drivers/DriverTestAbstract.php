<?php
/**
 * Class DriverTestAbstract
 *
 * @created      21.04.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\DatabaseTest\Drivers;

use chillerlan\Database\Drivers\{DriverException, DriverInterface};
use chillerlan\DatabaseTest\DBTestAbstract;
use ReflectionClass;

abstract class DriverTestAbstract extends DBTestAbstract{

	abstract public function testGetDBResource():void;

	public function testInstance():void{
		// make sure we're disconnected
		$this->db->disconnect();

		// try to disconnect from a disconnected DB (should not throw any errors -> mysqli)
		$this::assertTrue($this->db->disconnect());

		// reconnect
		$this->db->connect();

		// connect while already connected (coverage)
		$this::assertSame($this->db, $this->db->connect());

		$this::assertInstanceOf(DriverInterface::class, $this->db);
		$this::assertInstanceOf($this->driverFQCN, $this->db);

		$info = [
			'driver' => (new ReflectionClass($this->db))->getName(),
			'client' => $this->db->getClientInfo(),
			'server' => $this->db->getServerInfo(),
		];

		$this->logger->info('driver info', $info);

		// trigger the destructor (coverage)
		unset($this->db);
	}

	public function testEscapeString():void{
		// https://xkcd.com/327/
		$this::assertSame(
			"x'526f6265727427293b2044524f50205441424c452053747564656e74733b202d2d'",
			$this->db->escape("Robert'); DROP TABLE Students; --")
		);
	}

	public function testEscapeEmptyString():void{
		$this::assertSame("''", $this->db->escape(''));
	}

	public function testEscapeNull():void{
		$this::assertSame('null', $this->db->escape(null));
	}

	public function testEscapeBool():void{
		$this::assertSame(1, $this->db->escape(true));
		$this::assertSame(0, $this->db->escape(false));
	}

	public function testEscapeNumeric():void{
		$this::assertSame(1337, $this->db->escape('1337'));
		$this::assertSame(13.37, $this->db->escape('13.37'));
		$this::assertSame(1330000000.0, $this->db->escape('133e7'));
	}

	public function testGetInfoDisconnectedMessage():void{
		$this::assertTrue($this->db->disconnect());

		$msg = 'disconnected, no info available';

		$this::assertSame($msg, $this->db->getClientInfo());
		$this::assertSame($msg, $this->db->getServerInfo());
	}

	public function testRawEmptySQLException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('sql error: empty sql');

		$this->db->raw('');
	}

	public function testRawSQLErrorException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('sql error:');

		$this->db->raw('SELECT foo bar');
	}

	public function testPreparedEmptySQLException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('sql error: empty sql');

		$this->db->prepared('');
	}

	public function testPreparedSQLErrorException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('sql error:');

		$this->db->prepared('SELECT foo bar ???');
	}

	public function testMultiEmptySQLException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('sql error: empty sql');

		$this->db->multi('', []);
	}

	public function testMultiSQLErrorException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('sql error:');

		$this->db->multi('UPDATE foo bar ???', [[0]]);
	}

	public function testMultiInvalidDataException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('invalid data');


		$this->db->multi('UPDATE foo bar ???', []);
	}

	public function testMultiCallbackEmptySQLException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('sql error: empty sql');

		$this->db->multiCallback('', [], function(){});
	}

	public function testMultiCallbackSQLErrorException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('sql error:');

		$this->db->multiCallback('UPDATE foo bar ???', [[0]], function($r){ return $r; });
	}

	public function testMultiCallbackInvalidDataException():void{
		$this->expectException(DriverException::class);
		$this->expectExceptionMessage('invalid data');

		$this->db->multiCallback('UPDATE foo bar ???', [], function($r){ return $r; });
	}

}
