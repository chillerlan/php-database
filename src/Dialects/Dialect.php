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

namespace chillerlan\Database\Dialects;

interface Dialect{

	/**
	 *
	 */
	public function quote(string $str):string;

	/**
	 * @param array       $cols
	 * @param array       $from
	 * @param string|null $where
	 * @param mixed       $limit
	 * @param mixed       $offset
	 * @param bool|null   $distinct
	 * @param array|null  $groupby
	 * @param array|null  $orderby
	 *
	 * @return array
	 */
	public function select(array $cols, array $from, string $where = null, $limit = null, $offset = null, bool $distinct = null, array $groupby = null, array $orderby = null):array;

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
	 * @return mixed
	 */
	public function selectCount(array $from, string $where = null, bool $distinct = null, array $groupby = null); // @todo: offset?

	/**
	 *
	 */
	public function insert(string $table, array $fields, string $onConflict = null, string $conflictTarget = null):array;

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
	public function createDatabase(string $dbname, bool $ifNotExists = null, string $collate = null):array;

	/**
	 *
	 */
	public function createTable(string $table, array $cols, string $primaryKey = null, bool $ifNotExists = null, bool $temp = null, string $dir = null):array;

	/**
	 * @return mixed
	 */
	public function dropDatabase(string $dbname, bool $ifExists);

	/**
	 *
	 */
	public function dropTable(string $table, bool $ifExists):array;

	/**
	 *
	 */
	public function truncate(string $table):array;

	/**
	 * @param string      $name
	 * @param string      $type
	 * @param mixed       $length
	 * @param string|null $attribute
	 * @param string|null $collation
	 * @param bool|null   $isNull
	 * @param string|null $defaultType
	 * @param mixed       $defaultValue
	 * @param string|null $extra
	 *
	 * @return string
	 */
	public function fieldspec(string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null):string;

	/**
	 * @param string    $name
	 * @param array     $values
	 * @param mixed     $defaultValue
	 * @param bool|null $isNull
	 *
	 * @return string
	 */
	public function enum(string $name, array $values, $defaultValue = null, bool $isNull = null):string;

	/**
	 *
	 */
	public function showDatabases():array;

	/**
	 *
	 */
	public function showTables(string $database = null, string $pattern = null, string $where = null):array;

	/**
	 *
	 */
	public function showCreateTable(string $table):array;
}
