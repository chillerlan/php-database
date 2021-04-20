<?php
/**
 * @filesource   querybuilder.php
 * @created      07.07.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseExample;

use chillerlan\Database\Database;

/** @var \chillerlan\Database\DatabaseOptions $options */
$options = null;

/** @var \Psr\SimpleCache\CacheInterface $cache */
$cache   = null;

require_once __DIR__.'/common.php';

$db = new Database($options, $cache);
$db->connect();

$db->create
	->table('products')
	->ifNotExists()
	->int('id',10, null, false, 'UNSIGNED AUTO_INCREMENT')
	->tinytext('name', null, false)
	->varchar('type', 20)
	->decimal('price', '9,2', 0)
	->decimal('weight', '8,3')
	->int('added', 10, 0, null, 'UNSIGNED')
	->primaryKey('id')
	->query();

$db->truncate->table('products')->query();


// single row insert
$db->insert
	->into('products')
	->values(['name' => 'product1', 'type' => 'a', 'price' => 3.99, 'weight' => 0.1, 'added' => time()])
	->query();


// multi insert
$values = [
	['name' => 'product2', 'type' => 'b', 'price' => 4.20, 'weight' => 2.35, 'added' => time()],
	['name' => 'product3', 'type' => 'b', 'price' => 6.50, 'weight' => 1.725, 'added' => time()],
];

$db->insert
	->into('products')
	->values($values)
	->multi();


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
	->callback($values, function($row){
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
	->query('uid')
	->__toArray();

var_dump($result);


