<?php
/**
 * @filesource   common.php
 * @created      07.07.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseExample;

use chillerlan\Database\Connection;
use chillerlan\Database\Drivers\Native\MySQLiDriver;
use chillerlan\Database\Options;
use chillerlan\Database\Query\Dialects\MySQLQueryBuilder;
use chillerlan\SimpleCache\Cache;
use chillerlan\SimpleCache\Drivers\MemoryCacheDriver;
use chillerlan\Traits\DotEnv;

require_once __DIR__.'/../vendor/autoload.php';

$env = (new DotEnv(__DIR__.'/../config', '.env'))->load();


$cache = new Cache(new MemoryCacheDriver);

$options = new Options([
	'driver'       => MySQLiDriver::class,
	'querybuilder' => MySQLQueryBuilder::class,
	'host'         => $env->get('DB_MYSQLI_HOST'),
	'port'         => $env->get('DB_MYSQLI_PORT'),
	'socket'       => $env->get('DB_MYSQLI_SOCKET'),
	'database'     => $env->get('DB_MYSQLI_DATABASE'),
	'username'     => $env->get('DB_MYSQLI_USERNAME'),
	'password'     => $env->get('DB_MYSQLI_PASSWORD'),
]);

$db = new Connection($options, $cache);
$db->connect();


