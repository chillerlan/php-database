<?php
/**
 * Interface CreateTable
 *
 * @filesource   CreateTable.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Create
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Create;

use chillerlan\Database\Query\Statement;

/**
 * @link https://www.sqlite.org/lang_createtable.html
 * @link https://dev.mysql.com/doc/refman/5.7/en/create-table.html
 * @link https://www.postgresql.org/docs/current/static/sql-createtable.html
 * @link https://msdn.microsoft.com/library/ms174979(v=sql.110).aspx
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-ddl-tbl.html#fblangref25-ddl-tbl-create
 */
interface CreateTable extends Statement{

	/**
	 * @param $name
	 *
	 * @return \chillerlan\Database\Query\Create\CreateTable
	 */
#	public function index($name):CreateTable;

	/**
	 * @return \chillerlan\Database\Query\Create\CreateTable
	 */
	public function temp():CreateTable;

	/**
	 * @return \chillerlan\Database\Query\Create\CreateTable
	 */
	public function ifNotExists();

	/**
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\Create\CreateTable
	 */
	public function name(string $tablename);

	/**
	 * @param string $field
	 *
	 * @return \chillerlan\Database\Query\Create\CreateTable
	 */
	public function primaryKey(string $field):CreateTable;

	/**
	 * @param string $collation
	 *
	 * @return \chillerlan\Database\Query\Create\CreateTable
	 */
	public function charset(string $collation);

	/**
	 * @param string      $name
	 * @param string      $type
	 * @param null        $length
	 * @param string|null $attribute
	 * @param string|null $collation
	 * @param bool|null   $isNull
	 * @param string|null $defaultType
	 * @param mixed|null  $defaultValue
	 * @param string|null $extra
	 *
	 * @return \chillerlan\Database\Query\Create\CreateTable
	 */
	public function field(string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null):CreateTable;

	/**
	 * @param string      $name
	 * @param int|null    $length
	 * @param null        $defaultValue
	 * @param bool|null   $isNull
	 * @param string|null $attribute
	 *
	 * @return \chillerlan\Database\Query\Create\CreateTable
	 */
	public function tinyint(string $name, int $length = null, $defaultValue = null, bool $isNull = null, string $attribute = null):CreateTable;

	/**
	 * @param string      $name
	 * @param int|null    $length
	 * @param null        $defaultValue
	 * @param bool|null   $isNull
	 * @param string|null $attribute
	 *
	 * @return \chillerlan\Database\Query\Create\CreateTable
	 */
	public function int(string $name, int $length = null, $defaultValue = null, bool $isNull = null, string $attribute = null):CreateTable;

	/**
	 * @param string    $name
	 * @param null      $defaultValue
	 * @param bool|null $isNull
	 *
	 * @return \chillerlan\Database\Query\Create\CreateTable
	 */
	public function tinytext(string $name, $defaultValue = null, bool $isNull = null):CreateTable;

	/**
	 * @param string    $name
	 * @param null      $defaultValue
	 * @param bool|null $isNull
	 *
	 * @return \chillerlan\Database\Query\Create\CreateTable
	 */
	public function text(string $name, $defaultValue = null, bool $isNull = null):CreateTable;

	/**
	 * @param string    $name
	 * @param int       $length
	 * @param null      $defaultValue
	 * @param bool|null $isNull
	 *
	 * @return \chillerlan\Database\Query\Create\CreateTable
	 */
	public function varchar(string $name, int $length, $defaultValue = null, bool $isNull = null):CreateTable;

	/**
	 * @param string    $name
	 * @param string    $length
	 * @param null      $defaultValue
	 * @param bool|null $isNull
	 *
	 * @return \chillerlan\Database\Query\Create\CreateTable
	 */
	public function decimal(string $name, string $length, $defaultValue = null, bool $isNull = null):CreateTable;

	/**
	 * @param string    $name
	 * @param array     $values
	 * @param null      $defaultValue
	 * @param bool|null $isNull
	 *
	 * @return \chillerlan\Database\Query\Create\CreateTable
	 */
	public function enum(string $name, array $values, $defaultValue = null, bool $isNull = null):CreateTable;

}
