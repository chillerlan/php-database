<?php
/**
 * Interface Dialect
 *
 * @filesource   Dialect.php
 * @created      11.01.2018
 * @package      chillerlan\Database\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Dialects;

interface Dialect{

	/**
	 *
	 */
	public function quote(string $str):string;

	/**
	 *
	 */
	public function select(
		array       $cols,
		array       $from,
		string|null $where = null,
		mixed       $limit = null,
		mixed       $offset = null,
		bool|null   $distinct = null,
		array|null  $groupby = null,
		array|null  $orderby = null,
	):array;

	/**
	 *
	 */
	public function cols(array $expressions):array;

	/**
	 *
	 */
	public function from(array $expressions):array;

	/**
	 *
	 */
	public function orderby(array $expressions):array;

	/**
	 * @todo: offset?
	 */
	public function selectCount(
		array       $from,
		string|null $where = null,
		bool|null   $distinct = null,
		array|null  $groupby = null,
	):array;

	/**
	 *
	 */
	public function insert(
		string      $table,
		array       $fields,
		string|null $onConflict = null,
		string|null $conflictTarget = null,
	):array;

	/**
	 *
	 */
	public function update(string $table, array $set, string $where):array;

	/**
	 *
	 */
	public function delete(string $table, string $where):array;

	/**
	 *
	 */
	public function createDatabase(string $dbname, bool|null $ifNotExists = null, string|null $collate = null):array;

	/**
	 *
	 */
	public function createTable(
		string      $table,
		array       $cols,
		string|null $primaryKey = null,
		bool|null   $ifNotExists = null,
		bool|null   $temp = null,
		string|null $dir = null,
	):array;

	/**
	 *
	 */
	public function dropDatabase(string $dbname, bool $ifExists):array;

	/**
	 *
	 */
	public function dropTable(string $table, bool $ifExists):array;

	/**
	 *
	 */
	public function truncate(string $table):array;

	/**
	 *
	 */
	public function fieldspec(
		string      $name,
		string      $type,
		mixed       $length = null,
		string|null $attribute = null,
		string|null $collation = null,
		bool|null   $isNull = null,
		string|null $defaultType = null,
		mixed       $defaultValue = null,
		string|null $extra = null,
	):string;

	/**
	 *
	 */
	public function enum(string $name, array $values, mixed $defaultValue = null, bool|null $isNull = null):string;

	/**
	 *
	 */
	public function showDatabases():array;

	/**
	 *
	 */
	public function showTables(string|null $database = null, string|null $pattern = null, string|null $where = null):array;

	/**
	 *
	 */
	public function showCreateTable(string $table):array;
}
