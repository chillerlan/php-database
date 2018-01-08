<?php
/**
 * Interface CreateTable
 *
 * @filesource   Create.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

interface CreateTable extends Statement{

	/**
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function temp():CreateTable;

	/**
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function ifNotExists():CreateTable;

	/**
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function name(string $tablename):CreateTable;

	/**
	 * @param string $field
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function primaryKey(string $field):CreateTable;

	/**
	 * @param string $collation
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function charset(string $collation):CreateTable;

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
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function field(string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null):CreateTable;

	/**
	 * @param $name
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
#	public function index($name):CreateTable;
	/**
	 * @param string      $name
	 * @param int|null    $length
	 * @param null        $defaultValue
	 * @param bool|null   $isNull
	 * @param string|null $attribute
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function tinyint(string $name, int $length = null, $defaultValue = null, bool $isNull = null, string $attribute = null):CreateTable;

	/**
	 * @param string      $name
	 * @param int|null    $length
	 * @param null        $defaultValue
	 * @param bool|null   $isNull
	 * @param string|null $attribute
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function int(string $name, int $length = null, $defaultValue = null, bool $isNull = null, string $attribute = null):CreateTable;

	/**
	 * @param string    $name
	 * @param null      $defaultValue
	 * @param bool|null $isNull
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function tinytext(string $name, $defaultValue = null, bool $isNull = null):CreateTable;

	/**
	 * @param string    $name
	 * @param null      $defaultValue
	 * @param bool|null $isNull
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function text(string $name, $defaultValue = null, bool $isNull = null):CreateTable;

	/**
	 * @param string    $name
	 * @param int       $length
	 * @param null      $defaultValue
	 * @param bool|null $isNull
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function varchar(string $name, int $length, $defaultValue = null, bool $isNull = null):CreateTable;

	/**
	 * @param string    $name
	 * @param string    $length
	 * @param null      $defaultValue
	 * @param bool|null $isNull
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function decimal(string $name, string $length, $defaultValue = null, bool $isNull = null):CreateTable;

	/**
	 * @param string    $name
	 * @param array     $values
	 * @param null      $defaultValue
	 * @param bool|null $isNull
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function enum(string $name, array $values, $defaultValue = null, bool $isNull = null):CreateTable;

}
