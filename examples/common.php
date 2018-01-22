<?php
/**
 * @filesource   common.php
 * @created      07.07.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseExample;

use chillerlan\Database\{
	Database,DatabaseOptions,Drivers\MySQLiDrv
};
use chillerlan\Logger\Log;
use chillerlan\Logger\LogOptions;
use chillerlan\Logger\Output\ConsoleLog;
use chillerlan\SimpleCache\{
	Cache, Drivers\MemoryCacheDriver
};
use chillerlan\Traits\DotEnv;
use Psr\Log\LogLevel;

require_once __DIR__.'/../vendor/autoload.php';

$env = (new DotEnv(__DIR__.'/../config', '.env'))->load();

$cache = new Cache(new MemoryCacheDriver);

$options = new DatabaseOptions([
	'driver'       => MySQLiDrv::class,
	'host'         => $env->get('DB_MYSQLI_HOST'),
	'port'         => $env->get('DB_MYSQLI_PORT'),
	'socket'       => $env->get('DB_MYSQLI_SOCKET'),
	'database'     => $env->get('DB_MYSQLI_DATABASE'),
	'username'     => $env->get('DB_MYSQLI_USERNAME'),
	'password'     => $env->get('DB_MYSQLI_PASSWORD'),
]);

$log = (new Log)->addInstance(new ConsoleLog(new LogOptions(['minLogLevel' => LogLevel::DEBUG])), 'console');


