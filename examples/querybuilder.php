<?php
/**
 * @filesource   querybuilder.php
 * @created      07.07.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseExample;

use chillerlan\Database\DatabaseOptions;
use chillerlan\Database\Drivers\MySQLiDrv;
use chillerlan\DotEnv\DotEnv;
use chillerlan\SimpleCache\MemoryCache;

require_once __DIR__.'/../vendor/autoload.php';

$env = (new DotEnv(__DIR__.'/../.config', '.env'))->load();

$options = new DatabaseOptions([
	'host'     => $env->get('DB_MYSQLI_HOST'),
	'port'     => (int)$env->get('DB_MYSQLI_PORT'),
	'socket'   => $env->get('DB_MYSQLI_SOCKET'),
	'database' => $env->get('DB_MYSQLI_DATABASE'),
	'username' => $env->get('DB_MYSQLI_USERNAME'),
	'password' => $env->get('DB_MYSQLI_PASSWORD'),
]);

$db = new MySQLiDrv($options, new MemoryCache);
$db->connect();

// named parameters for context
$db->create
	->table(tablename: 'products')
	->ifNotExists()
	->int(name: 'id', length: 10, isNull: false, attribute: 'UNSIGNED AUTO_INCREMENT')
	->tinytext(name: 'name', isNull:  false)
	->varchar(name: 'type', length:  20)
	->decimal(name: 'price', length: '9,2', defaultValue: 0)
	->decimal(name: 'weight', length: '8,3')
	->int(name: 'added', length: 10, defaultValue: 0, attribute: 'UNSIGNED')
	->primaryKey(field: 'id')
	->executeQuery();

$db->truncate
	->table('products')
	->executeQuery();


// single row insert
$db->insert
	->into('products')
	->values(['name' => 'product1', 'type' => 'a', 'price' => 3.99, 'weight' => 0.1, 'added' => time()])
	->executeQuery();


// multi insert
$values = [
	['name' => 'product2', 'type' => 'b', 'price' => 4.20, 'weight' => 2.35, 'added' => time()],
	['name' => 'product3', 'type' => 'b', 'price' => 6.50, 'weight' => 1.725, 'added' => time()],
];

$db->insert
	->into('products')
	->values($values)
	->executeMultiQuery();


// multi insert with callback
$values = [
	['product4', 'c', 1.49, 4.2,],
	['product5', 'a', 8.19, 6.56,],
	['product6', 'b', 5.00, 5.5,],
];

$db->insert
	->into('products')
	->values([
		['name' => '?', 'type' => '?', 'price' => '?', 'weight' => '?', 'added' => '?']
	])
	->executeMultiQuery($values, function(array $row):array{
		return [
			$row[0],
			$row[1],
			floatval($row[2]),
			floatval($row[3]),
			time(),
		];
	});


// select
$result = $db->select
	->cols([
		'uid'         => ['t1.id', 'md5'],
		'productname' => 't1.name',
		'price'       => 't1.price',
		'type'        => ['t1.type', 'upper'],
	])
	->from(['t1' => 'products'])
	->where('t1.type', 'a')
	->orderBy(['t1.price' => 'asc'])
	->executeQuery('uid')
	->toArray();

var_dump($result);


