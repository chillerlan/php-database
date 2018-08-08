# chillerlan/php-database

A PHP 7.2+ SQL client and querybuilder for the most common databases.

[![version][packagist-badge]][packagist]
[![license][license-badge]][license]
[![Travis][travis-badge]][travis]
[![Coverage][coverage-badge]][coverage]
[![Scrunitizer][scrutinizer-badge]][scrutinizer]
[![Packagist downloads][downloads-badge]][downloads]
[![PayPal donate][donate-badge]][donate]

[packagist-badge]: https://img.shields.io/packagist/v/chillerlan/php-database.svg?style=flat-square
[packagist]: https://packagist.org/packages/chillerlan/php-database
[license-badge]: https://img.shields.io/github/license/chillerlan/php-database.svg?style=flat-square
[license]: https://github.com/chillerlan/php-database/blob/master/LICENSE
[travis-badge]: https://img.shields.io/travis/chillerlan/php-database.svg?style=flat-square
[travis]: https://travis-ci.org/chillerlan/php-database
[coverage-badge]: https://img.shields.io/codecov/c/github/chillerlan/php-database.svg?style=flat-square
[coverage]: https://codecov.io/github/chillerlan/php-database
[scrutinizer-badge]: https://img.shields.io/scrutinizer/g/chillerlan/php-database.svg?style=flat-square
[scrutinizer]: https://scrutinizer-ci.com/g/chillerlan/php-database
[downloads-badge]: https://img.shields.io/packagist/dt/chillerlan/php-database.svg?style=flat-square
[downloads]: https://packagist.org/packages/chillerlan/php-database/stats
[donate-badge]: https://img.shields.io/badge/donate-paypal-ff33aa.svg?style=flat-square
[donate]: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WLYUNAT9ZTJZ4

# Documentation

## Requirements
- PHP 7.2+
- one of the supported databases, set up to work with PHP

## Supported databases
- [MySQL](https://dev.mysql.com/doc/refman/5.6/en/) (5.5+) / [MariaDB](https://mariadb.com/kb/en/library/basic-sql-statements/)
- [PostgreSQL](https://www.postgresql.org/docs/9.5/static/index.html) (9.5+)
- [SQLite3](https://www.sqlite.org/lang.html)
- [Firebird](https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25.html) (2.5+)
- [Microsoft SQL Server](https://github.com/Microsoft/msphpsql) ([transact-sql](https://docs.microsoft.com/sql/t-sql/language-reference))
- any other database supported via PDO, ODBC or native PHP extension

## Installation
**requires [composer](https://getcomposer.org)**

### *composer.json*
 (note: replace `dev-master` with a [version boundary](https://getcomposer.org/doc/articles/versions.md#summary))
```json
{
	"require": {
		"php": ">=7.2.0",
		"chillerlan/database": "^2.1"
	}
}
```

### Manual installation
Download the desired version of the package from [master](https://github.com/chillerlan/php-database/archive/master.zip) or 
[release](https://github.com/chillerlan/php-database/releases) and extract the contents to your project folder. After that:
- run `composer install` to install the required dependencies and generate `/vendor/autoload.php`.
- if you use a custom autoloader, point the namespace `chillerlan\Database` to the folder `src` of the package 

Profit!

## Usage

### Getting started
Both, the `DriverInterface` and `QueryBuilder` can be instanced on their own. 
However, since the `QueryBuilder` requires an instance of `DriverInterface` it's recommended to just use `Database` which instances both and provides all of their methods.
A `DriverInterface` requires a `DatabaseOptions` object and accepts a `Psr\SimpleCache\CacheInterface` and a `Psr\Log\LoggerInterface` as optional parameters, the `QueryBuilder` accepts a `LoggerInterface` as additional parameter.

```php
$options = new DatabaseOptions;
$options->database = 'whatever';
$options->username = 'user';
$options->password = 'supersecretpassword';
```
which is equivalent to
```php
$options = new DatabaseOptions([
	'database' => 'whatever',
	'username' => 'user',
	'password' => 'supersecretpassword',
]);
```
now instance a driver with these options
```php
$mysql = new MySQLiDrv($options, $cache, $log);
$mysql->connect();

// a raw query using the driver directly
$result = $mysql->raw('SELECT * FROM sometable');
```
via the querybuilder

```php
$querybuilder = new QueryBuilder($mysql, $log)

$result = $querybuilder->select->from('sometable')->query();
```
recommended way via `Database`, which provides all methods of `DriverInterface` and `QueryBuilder`
```php
$options->driver = MySQLiDrv::class;

$db = new Database($options);
$db->connect();

$result = $db->raw('SELECT * FROM sometable');
// is equivalent to
$result = $db->select->from('sometable')->query();
```

###  Properties of `DatabaseOptions`

property | type | default | allowed | description
-------- | ---- | ------- | ------- | -----------
`$driver` | string | `null` | `DriverInterface` | database driver to use (FQCN)
`$querybuilder` | string | `null` | `QueryBuilderInterface` | query builder to use (FQCN) [optional]
`$host` | string | 'localhost' |  |
`$port` | int | `null` |  |
`$socket` | string | `null` |  |
`$database` | string | `null` |  |
`$username` | string | `null` |  |
`$password` | string | `null` |  |
`$use_ssl` | bool | `false` |  | indicates whether the connection should use SSL or not
`$ssl_key` | string | `null` |  |
`$ssl_cert` | string | `null` |  |
`$ssl_ca` | string | `null` |  |
`$ssl_capath` | string | `null` |  |
`$ssl_cipher` | string | `null` |  |
`$mysqli_timeout` | int | 3 |  |
`$mysql_charset` | string | 'utf8mb4' |  | [How to support full Unicode in MySQL](https://mathiasbynens.be/notes/mysql-utf8mb4)
`$pgsql_charset` | string | 'UTF8' |  |
`$odbc_driver` | string | `null` |  |
`$convert_encoding_src` | string | `null` | [supported encodings](http://php.net/manual/mbstring.supported-encodings.php) | `mb_convert_encoding()`, used in `Result`
`$convert_encoding_dest` | string | 'UTF-8' | [supported encodings](http://php.net/manual/mbstring.supported-encodings.php) | `mb_convert_encoding()`, used in `Result`
`$mssql_timeout` | int | 3 |  |
`$mssql_charset` | string | 'UTF-8' |  |
`$mssql_encrypt` | bool | `false` |  |

### Methods of `DriverInterface`

method | return 
------ | ------
`__construct(DatabaseOptions $options, CacheInterface $cache = null)` | -
`connect()` | `DriverInterface`
`disconnect()` | `bool`
`getDBResource()` | <code>resource&#124;object</code>
`getClientInfo()` | `string`
`getServerInfo()` | `string`
`escape($data)` | `string` (subject to change)
`raw(string $sql, string $index = null, bool $assoc = true)` | <code>Result&#124;bool</code>
`rawCached(string $sql, string $index = null, bool $assoc = true, int $ttl = null)` | <code>Result&#124;bool</code>
`prepared(string $sql, array $values = [], string $index = null, bool $assoc = true)` | <code>Result&#124;bool</code>
`preparedCached(string $sql, array $values = [], string $index = null, bool $assoc = true, int $ttl = null)` | <code>Result&#124;bool</code>
`multi(string $sql, array $values)` | `bool` (subject to change)
`multiCallback(string $sql, array $data, $callback)` | `bool` (subject to change)

### Methods of `QueryBuilder`
All methods of `QueryBuilder` are also accessible as properties via magic methods.
The returned object is a `Statement` of `\chillerlan\Database\Query\*` interfaces.

method | return 
------ | ------
`__construct(DriverInterface $db, LoggerInterface $logger = null)` | -
`select()` | `Select`
`insert()` | `Insert`
`update()` | `Update`
`delete()` | `Delete`
`create()` | `Create`
`alter()`  | `Alter`
`drop()`   | `Drop`

### Methods of `Database`
in addition to `DriverInterface` and `QueryBuilderInterface` methods

method | return 
------ | ------
`__construct(DatabaseOptions $options, CacheInterface $cache = null, LoggerInterface $logger = null)` | -
`getDriver()` | `DriverInterface`
`getQueryBuilderFQCN()` | <code>QueryBuilderInterface&#124;null</code>

## The `Statement` interface

method | return | description 
------ | ------ | -----------
`sql()` | `string` | returns the SQL for the current statement
`bindValues()` | `array` | returns the values for each '?' parameter in the SQL
`query(string $index = null` | `Result` | Executes the current statement. `$index` is being used in "SELECT" statements to determine a column to index the `Result` by. `$values`.
`multi(array $values = null)` | bool | Executes the current statement as multi query. `$values` needs to be a multi dimensional array with each row.
`callback(array $values = null, $callback = null)` | bool | Executes the current statement. `$index` is being used in "SELECT" statements to determine a column to index the `Result` by. `$values` and `$callback` can be used to provide multiple values on multi row "INSERT" or "UPDATE" queries.


### `Create`

method | return 
------ | ------
`database(string $dbname = null)` | `CreateDatabase`
`table(string $tablename = null)` | `CreateTable`

#### `CreateDatabase`

method | description 
------ | -----------
`ifNotExists()` | 
`name(string $dbname = null)` | 
`charset(string $collation)` | 

```php
$conn->create
	->database('test')
	->ifNotExists()
	->charset('utf8mb4_bin')
	->query();
```
```mysql
CREATE DATABASE IF NOT EXISTS `test` CHARACTER SET utf8mb4 COLLATE utf8mb4_bin
```

#### `CreateTable`

method | description 
------ | -----------
`ifNotExists()` |
`name(string $tablename = null)` | 
`charset(string $collation)` | 
`primaryKey(string $field)` | 
`field(string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null)` |
`int(string $name, int $length = null,  $defaultValue = null , bool $isNull = null, string $attribute = null)` | convenience shortcut for `field()`, also `tinyint(...)`
`varchar(string $name, int $length,  $defaultValue = null , bool $isNull = null)` | 
`decimal(string $name, string $length,  $defaultValue = null , bool $isNull = null)` | 
`text(string $name,  $defaultValue = null , bool $isNull = true)` | also `tinytext()`
`enum(string $name, array $values, $defaultValue = null , bool $isNull = null)` | currently the only way to create an "ENUM" field

```php
$conn->create
	->table('products')
	->ifNotExists()
	->int('id', 10, null, false, 'UNSIGNED AUTO_INCREMENT')
	->tinytext('name', null, false)
	->varchar('type', 20)
	->decimal('price', '9,2', 0)
	->decimal('weight', '8,3')
	->int('added', 10, 0, null, 'UNSIGNED')
	->primaryKey('id')
	->query();
```
The generated Query will look something like this
```mysql
-- mysql

CREATE TABLE IF NOT EXISTS `products` (
	`id` INT(10) UNSIGNED AUTO_INCREMENT NOT NULL,
	`name` TINYTEXT NOT NULL,
	`type` VARCHAR(20),
	`price` DECIMAL(9,2) NOT NULL DEFAULT 0,
	`weight` DECIMAL(8,3),
	`added` INT(10) UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
)
```
Note that additional constraints and attributes will be appended regardless of the Query dialect 
```postgresql
-- postgres: attributes UNSIGNED and AUTO_INCREMENT are invalid

CREATE TABLE IF NOT EXISTS "products" (
	"id" INT NOT NULL UNSIGNED AUTO_INCREMENT,
	"name" VARCHAR(255) NOT NULL,
	"type" VARCHAR(20),
	"price" DECIMAL(9,2) NOT NULL DEFAULT '0',
	"weight" DECIMAL(8,3),
	"added" INT NOT NULL UNSIGNED DEFAULT '0',
	PRIMARY KEY ("id")
)
```


### `Insert`

method | description 
------ | -----------
`into(string $table)` | The table where to insert data
`values(array $values)` | An array of values where each row represents a row to insert `[['column' => 'value', ...], ...]`

```php
$conn->insert
	->into('products')
	->values(['name' => 'product1', 'type' => 'a', 'price' => 3.99, 'weight' => 0.1, 'added' => time()])
	->query();
```
```mysql
INSERT INTO `products` (`name`, `type`, `price`, `weight`, `added`) VALUES (?,?,?,?,?)
```


```php
$values = [
	['name' => 'product2', 'type' => 'b', 'price' => 4.20, 'weight' => 2.35, 'added' => time()],
	['name' => 'product3', 'type' => 'b', 'price' => 6.50, 'weight' => 1.725, 'added' => time()],
];

$conn->insert
	->into('products')
	->multi($values);
```
As an alternative, you can provide the values via a callback
```php
$values = [
	['product4', 'c', 3.99, 0.1,],
	['product5', 'a', 4.20, 2.35,],
	['product6', 'b', 6.50, 1.725,],
];

$conn->insert
	->into('products')
	->values([['name' => '?', 'type' => '?', 'price' => '?', 'weight' => '?', 'added' => '?']])
	->callback($values, function($row){
		return [
			$row[0],
			$row[1],
			floatval($row[2]),
			floatval($row[3]),
			time(),
		];
	});
```


### `Select`

method | description 
------ | -----------
`distinct()` | sets the "DISTINCT" statement (if the Query dialect supports it)
`cols(array $expressions)` | An array of column expressions. If omitted, a `SELECT * ...` will be performed. Example: `['col', 'alias' => 'col', 'alias' => ['col', 'sql_function']]`
`from(array $expressions)` | An array of table expressions. Example: `['table', 'alias' => 'table']`
`groupBy(array $expressions)` | An array of expressions to group by.
`where($val1, $val2, $operator = '=', $bind = true, $join = 'AND')` | Adds a "WHERE" clause, comparing `$val1` and `$val2` by `$operator`. `$bind` specifies whether the value should be bound to a '?' parameter (default) or not (no effect if `$val2` is a `Select` interface). If more than one "WHERE" statement exists, they will be joined by `$join`.
`openBracket($join = null)` | puts an opening bracket `(` at the current position in the "WHERE" statement
`closeBracket()` | puts a closing bracket `)` at the current position in the "WHERE" statement
`orderBy(array $expressions)` | An array of expressions to order by. `['col1', 'col2' => 'asc', 'col3' => 'desc']`
`offset(int $offset)` | Sets the offset to start from
`limit(int $limit)` | Sets a row limit (page size)
`count()` | Executes the statement to perform a `SELECT COUNT(*) ...` and returns the row count as int
`cached()` | Performs a chached query

```php
$result = $conn->select
	->cols([
		'uid'         => ['t1.id', 'md5'],
		'productname' => 't1.name',
		'price'       => 't1.price',
		'type'        => ['t1.type', 'upper'],
	])
	->from(['t1' => 'products'])
	->where('t1.type', 'a')
	->orderBy(['t1.price' => 'asc'])
	->execute('uid')
	->__toArray();
```

```mysql
SELECT MD5(`t1`.`id`) AS `uid`,
	`t1`.`name` AS `productname`,
	`t1`.`price` AS `price`,
	UPPER(`t1`.`type`) AS `type`
FROM `products` AS `t1`
WHERE `t1`.`type` = ?
ORDER BY `t1`.`price` ASC
```

```
array(2) {
  'c4ca4238a0b923820dcc509a6f75849b' =>
  array(4) {
    'uid' =>
    string(32) "c4ca4238a0b923820dcc509a6f75849b"
    'productname' =>
    string(8) "product1"
    'price' =>
    string(4) "3.99"
    'type' =>
    string(1) "A"
  }
  'e4da3b7fbbce2345d7772b0674a318d5' =>
  array(4) {
    'uid' =>
    string(32) "e4da3b7fbbce2345d7772b0674a318d5"
    'productname' =>
    string(8) "product5"
    'price' =>
    string(4) "8.19"
    'type' =>
    string(1) "A"
  }
}
```


### `Update`

method | description 
------ | -----------
`table(string $tablename)` | The table to update
`set(array $set, bool $bind = true)` | `$set` is a key/value array to update the table with. `$bind` determines whether the values should be inserted into the Query (unsafe! use only for aliases) or be replaced by parameters (the default). 
`where($val1, $val2, $operator = '=', $bind = true, $join = 'AND')` | see `Select::where()`
`openBracket($join = null)` |  see `Select::openBracket()`
`closeBracket()` |  see `Select::closeBracket()`

Single row update
```php
$db->update
	->table('table_to_update')
	->set(['col_to_update' => 'val1'])
	->where('row_id', 1)
	->query();
```

Update multiple rows
```php
$values = [
	// [col_to_update, row_id]
	['val1', 1],
	['val2', 2],
	['val3', 3],
];

$db->update
	->table('table_to_update')
	->set(['col_to_update' => '?'], false) // disable value binding here
	->where('row_id', '?', '=', false) // also disable binding here
	->multi($values);
```

The generated SQL for both examples would look like the following, the difference is that one performs a single query, while the other loops through the given value array in the open prepared statement.
```mysql
UPDATE `table_to_update` SET `col_to_update` = ? WHERE `row_id` = ?
```

### `Delete`

method | description 
------ | -----------
`from(string $table)` | The table to delete from
`where($val1, $val2, $operator = '=', $bind = true, $join = 'AND')` | see `Select::where()`
`openBracket($join = null)` |  see `Select::openBracket()`
`closeBracket()` |  see `Select::closeBracket()`


## The `Result` and `ResultRow` objects
`Result` implements `\SeekableIterator`, `\ArrayAccess` and `\Countable`, `ResultRow` extends it.

### `Result`

property | description 
-------- | -----------
`length` | 

#### methods in addition to `\SeekableIterator`, `\ArrayAccess` and `\Countable`

method | description 
------ | -----------
`__construct($data = null, $sourceEncoding = null, $destEncoding = 'UTF-8')` | If `$data` is of type `\Traversable`, `\stdClass` or `array`, the `Result` will be filled with its values. If `$sourceEncoding` is present, the values will be converted to `$destEncoding` via `mb_convert_encoding()`.
`__merge(Result $result)` | merges one `Result` object into another (using `array_merge()`)
`__chunk(int $size)` | splits the `Result` into chunks of `$size` and returns it as `array` (using `array_chunk()`)

#### methods from `Enumerable`

method | description 
------ | -----------
`__toArray()` | returns an `array` representation of the `Result`
`__map($callback)` | collects the result of `$callback` for each value of `Result` and returns it as `array`
`__each($callback)` | similar to `__map()`, except it doesn't collect results and returns the `Result` instance
`__reverse()` | reverses the order of the `Result` (using `array_reverse()`)

### `ResultRow`
`ResultRow` allows to call the result fields as magic methods or properties. 
If called as method, you may supply a `callable` as argument which then takes the field value as argument. Fancy, huh?

### `Result` and `ResultRow` examples

#### `__map()` and `__each()`

```php
$values = $result->__map(function($row){

	// ...
	
	return [
		$row->id,
		$row->name('trim'),
		// ...
	];
});
```

#### `__merge()`, `__toArray()`, `__chunk()` and `__reverse()`

```php
$result1 = new Result([['id' => 1]]);
$result2 = new Result([['id' => 2]]);

$result1->__merge($result2);

var_dump($result1->__toArray()); 
// -> [['id' => 1], ['id' => 2]]

var_dump($result1->__reverse()->__chunk(1)[0]);
// -> [['id' => 2]]
```

