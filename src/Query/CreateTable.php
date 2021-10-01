<?php
/**
 * Interface CreateTable
 *
 * @filesource   CreateTable.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

/**
 * @method string sql(bool $multi = null)
 * @method array  getBindValues()
 * @method mixed  query(string $index = null)
 */
interface CreateTable extends Statement{

	/**
	 * @param $name
	 *
	 * @return \chillerlan\Database\Query\CreateTable
	 */
#	public function index($name):CreateTable;

#	public function timestamp():CreateTable{}

	/**
	 *
	 */
	public function temp():CreateTable;

	/**
	 *
	 */
	public function ifNotExists();

	/**
	 *
	 */
	public function name(string $tablename);

	/**
	 *
	 */
	public function primaryKey(string $field, string $dir = null):CreateTable;

	/**
	 *
	 */
	public function charset(string $collation);

	/**
	 *
	 */
	public function field(string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null):CreateTable;

	public function tinyint(string $name, int $length = null, $defaultValue = null, bool $isNull = null, string $attribute = null):CreateTable;
	public function int(string $name, int $length = null, $defaultValue = null, bool $isNull = null, string $attribute = null):CreateTable;
	public function bigint(string $name, int $length = null, $defaultValue = null, bool $isNull = null, string $attribute = null):CreateTable;

	public function tinytext(string $name, $defaultValue = null, bool $isNull = null):CreateTable;
	public function text(string $name, $defaultValue = null, bool $isNull = null):CreateTable;
	public function mediumtext(string $name, $defaultValue = null, bool $isNull = null):CreateTable;
	public function longtext(string $name, $defaultValue = null, bool $isNull = null):CreateTable;

	public function varchar(string $name, int $length, $defaultValue = null, bool $isNull = null):CreateTable;
	public function decimal(string $name, string $length, $defaultValue = null, bool $isNull = null):CreateTable;
	public function enum(string $name, array $values, $defaultValue = null, bool $isNull = null):CreateTable;

}
