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
	 * @param string $str
	 *
	 * @return string
	 */
	public function quote(string $str):string;

	/**
	 * @param array       $cols
	 * @param array       $from
	 * @param string|null $where
	 * @param null        $limit
	 * @param null        $offset
	 * @param bool|null   $distinct
	 * @param array       $groupby
	 * @param array       $orderby
	 *
	 * @return array
	 */
	public function select(array $cols, array $from, string $where = null, $limit = null, $offset = null, bool $distinct = null, array $groupby, array $orderby):array;

	/**
	 * @param array $expressions
	 *
	 * @return array
	 */
	public function cols(array $expressions):array;

	/**
	 * @param array $expressions
	 *
	 * @return array
	 */
	public function from(array $expressions):array;

	/**
	 * @param array $expressions
	 *
	 * @return array
	 */
	public function orderby(array $expressions):array;

	/**
	 * @param array       $from
	 * @param string|null $where
	 * @param bool|null   $distinct
	 * @param array|null  $groupby
	 *
	 * @return mixed
	 */
	public function selectCount(array $from, string $where = null, bool $distinct = null, array $groupby = null); // @todo: offset?

	/**
	 * @param string      $table
	 * @param array       $fields
	 * @param string|null $onConflict
	 * @param string|null $conflictTarget
	 *
	 * @return array
	 */
	public function insert(string $table, array $fields, string $onConflict = null, string $conflictTarget = null):array;

	/**
	 * @param string $table
	 * @param array  $set
	 * @param string $where
	 *
	 * @return array
	 */
	public function update(string $table, array $set, string $where):array;

	/**
	 * @param string $table
	 * @param string $where
	 *
	 * @return array
	 */
	public function delete(string $table, string $where):array;

	/**
	 * @param string      $dbname
	 * @param bool|null   $ifNotExists
	 * @param string|null $collate
	 *
	 * @return array
	 */
	public function createDatabase(string $dbname, bool $ifNotExists = null, string $collate = null):array;

	/**
	 * @param string      $table
	 * @param array       $cols
	 * @param string|null $primaryKey
	 * @param bool        $ifNotExists
	 * @param bool        $temp
	 * @param string|null $dir
	 *
	 * @return array
	 */
	public function createTable(string $table, array $cols, string $primaryKey = null, bool $ifNotExists, bool $temp, string $dir = null):array;

	/**
	 * @param string $dbname
	 * @param bool   $ifExists
	 *
	 * @return mixed
	 */
	public function dropDatabase(string $dbname, bool $ifExists);

	/**
	 * @param string $table
	 * @param bool   $ifExists
	 *
	 * @return array
	 */
	public function dropTable(string $table, bool $ifExists):array;

	/**
	 * @param string $table
	 *
	 * @return array
	 */
	public function truncate(string $table):array;

	/**
	 * @param string      $name
	 * @param string      $type
	 * @param null        $length
	 * @param string|null $attribute
	 * @param string|null $collation
	 * @param bool|null   $isNull
	 * @param string|null $defaultType
	 * @param null        $defaultValue
	 * @param string|null $extra
	 *
	 * @return string
	 */
	public function fieldspec(string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null):string;

	/**
	 * @param string    $name
	 * @param array     $values
	 * @param null      $defaultValue
	 * @param bool|null $isNull
	 *
	 * @return string
	 */
	public function enum(string $name, array $values, $defaultValue = null, bool $isNull = null):string;

	/**
	 * @return array
	 */
	public function showDatabases():array;

	/**
	 * @param string|null $database
	 * @param string|null $pattern
	 * @param string|null $where
	 *
	 * @return array
	 */
	public function showTables(string $database = null, string $pattern = null, string $where = null):array;

	/**
	 * @param string $table
	 *
	 * @return array
	 */
	public function showCreateTable(string $table):array;

}
