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
use Dotenv\Dotenv;

require_once __DIR__.'/../vendor/autoload.php';

(new Dotenv(__DIR__.'/../config', '.env'))->load();


$cache = new Cache(new MemoryCacheDriver);

$options = new Options([
	'driver'       => MySQLiDriver::class,
	'querybuilder' => MySQLQueryBuilder::class,
	'host'         => getenv('DB_MYSQLI_HOST'),
	'port'         => getenv('DB_MYSQLI_PORT'),
	'socket'       => getenv('DB_MYSQLI_SOCKET'),
	'database'     => getenv('DB_MYSQLI_DATABASE'),
	'username'     => getenv('DB_MYSQLI_USERNAME'),
	'password'     => getenv('DB_MYSQLI_PASSWORD'),
]);

$db = new Connection($options, $cache);
$db->connect();


