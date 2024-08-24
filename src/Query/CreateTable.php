<?php
/**
 * Class CreateTable
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use function is_scalar, strtoupper, trim;

/**
 * @link https://www.sqlite.org/lang_createtable.html
 * @link https://dev.mysql.com/doc/refman/5.7/en/create-table.html
 * @link https://www.postgresql.org/docs/current/static/sql-createtable.html
 * @link https://docs.microsoft.com/en-gb/sql/t-sql/statements/create-table-transact-sql
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-ddl-tbl.html#fblangref25-ddl-tbl-create
 *
 * @link https://www.postgresql.org/docs/current/static/datatype.html
 * @link https://docs.microsoft.com/sql/t-sql/data-types/data-types-transact-sql
 */
class CreateTable extends Statement implements Query, IfNotExists{

	protected bool $temp = false;
	protected string|null  $primaryKey = null;
	protected array $cols = [];
	protected string|null  $dir = null;

	public function name(string $name):CreateTable{
		return $this->setName($name);
	}

	public function charset(string $charset):CreateTable{
		return $this->setCharset($charset);
	}

	public function ifNotExists():CreateTable{
		return $this->setIfNotExists();
	}

	/** @inheritdoc */
	protected function getSQL():array{
		return $this->dialect->createTable($this->name, $this->cols, $this->primaryKey, $this->ifNotExists, $this->temp, $this->dir);
	}

	public function temp():CreateTable{
		$this->temp = true;

		return $this;
	}

	public function primaryKey(string $field, string|null $dir = null):CreateTable{
		$this->primaryKey = trim($field);
		$this->dir        = strtoupper($dir ?? '');

		return $this;
	}

	public function field(string $name, string $type, mixed $length = null, string|null $attribute = null, string|null $collation = null, bool|null $isNull = null, string|null $defaultType = null, mixed $defaultValue = null, string|null $extra = null):CreateTable{

		if(is_scalar($defaultValue) && $defaultType === null){
			$defaultType = 'USER_DEFINED';
		}

		$this->cols[$name] = $this->dialect->fieldspec($name, $type, $length, $attribute, $collation, $isNull, $defaultType, $defaultValue, $extra);

		return $this;
	}

	public function enum(string $name, array $values, mixed $defaultValue = null, bool|null $isNull = null):CreateTable{
		$this->cols[$name] = $this->dialect->enum($name, $values, $defaultValue, $isNull);

		return $this;
	}

	public function tinyint(string $name, int|null $length = null, mixed $defaultValue = null, bool|null $isNull = null, string|null $attribute = null):CreateTable{
		return $this->field($name, 'TINYINT', $length, $attribute, null, $isNull, null, $defaultValue);
	}

	public function int(string $name, int|null $length = null, mixed $defaultValue = null, bool|null $isNull = null, string|null $attribute = null):CreateTable{
		return $this->field($name, 'INT', $length, $attribute, null, $isNull, null, $defaultValue);
	}

	public function bigint(string $name, int|null $length = null, mixed $defaultValue = null, bool|null $isNull = null, string|null $attribute = null):CreateTable{
		return $this->field($name, 'BIGINT', $length, $attribute, null, $isNull, null, $defaultValue);
	}

	public function varchar(string $name, int $length, mixed $defaultValue = null, bool|null $isNull = null):CreateTable{
		return $this->field($name, 'VARCHAR', $length, null, null, $isNull, null, $defaultValue);
	}

	public function decimal(string $name, string $length, mixed $defaultValue = null, bool|null $isNull = null):CreateTable{
		return $this->field($name, 'DECIMAL', $length, null, null, $isNull, null, $defaultValue);
	}

	public function tinytext(string $name, mixed $defaultValue = null, bool|null $isNull = null):CreateTable{
		return $this->field($name, 'TINYTEXT', null, null, null, $isNull, null, $defaultValue);
	}

	public function text(string $name, mixed $defaultValue = null, bool|null $isNull = null):CreateTable{
		return $this->field($name, 'TEXT', null, null, null, $isNull, null, $defaultValue);
	}

	public function mediumtext(string $name, mixed $defaultValue = null, bool|null $isNull = null):CreateTable{
		return $this->field($name, 'MEDIUMTEXT', null, null, null, $isNull, null, $defaultValue);
	}

	public function longtext(string $name, mixed $defaultValue = null, bool|null $isNull = null):CreateTable{
		return $this->field($name, 'LONGTEXT', null, null, null, $isNull, null, $defaultValue);
	}

}
