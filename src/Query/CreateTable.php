<?php
/**
 * Class CreateTable
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */
declare(strict_types=1);

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
class CreateTable extends StatementAbstract implements Query, IfNotExists{

	protected bool        $temp       = false;
	protected string|null $primaryKey = null;
	protected array       $cols       = [];
	protected string|null $dir        = null;

	public function name(string $name):static{
		return $this->setName($name);
	}

	public function charset(string $charset):static{
		return $this->setCharset($charset);
	}

	public function ifNotExists():static{
		return $this->setIfNotExists();
	}

	protected function getSQL():array{
		return $this->dialect->createTable($this->name, $this->cols, $this->primaryKey, $this->ifNotExists, $this->temp, $this->dir);
	}

	public function temp():static{
		$this->temp = true;

		return $this;
	}

	public function primaryKey(string $field, string|null $dir = null):static{
		$this->primaryKey = trim($field);
		$this->dir        = strtoupper($dir ?? '');

		return $this;
	}

	public function field(
		string      $name,
		string      $type,
		mixed       $length = null,
		string|null $attribute = null,
		string|null $collation = null,
		bool|null   $isNull = null,
		string|null $defaultType = null,
		mixed       $defaultValue = null,
		string|null $extra = null,
	):static{

		if(is_scalar($defaultValue) && $defaultType === null){
			$defaultType = 'USER_DEFINED';
		}

		$this->cols[$name] = $this->dialect->fieldspec($name, $type, $length, $attribute, $collation, $isNull, $defaultType, $defaultValue, $extra);

		return $this;
	}

	public function enum(string $name, array $values, mixed $defaultValue = null, bool|null $isNull = null):static{
		$this->cols[$name] = $this->dialect->enum($name, $values, $defaultValue, $isNull);

		return $this;
	}

	public function tinyint(string $name, int|null $length = null, mixed $defaultValue = null, bool|null $isNull = null, string|null $attribute = null):static{
		return $this->field(name: $name, type: 'TINYINT', length: $length, attribute: $attribute, isNull: $isNull, defaultValue: $defaultValue);
	}

	public function int(string $name, int|null $length = null, mixed $defaultValue = null, bool|null $isNull = null, string|null $attribute = null):static{
		return $this->field(name: $name, type: 'INT', length: $length, attribute: $attribute, isNull: $isNull, defaultValue: $defaultValue);
	}

	public function bigint(string $name, int|null $length = null, mixed $defaultValue = null, bool|null $isNull = null, string|null $attribute = null):static{
		return $this->field(name: $name, type: 'BIGINT', length: $length, attribute: $attribute, isNull: $isNull, defaultValue: $defaultValue);
	}

	public function varchar(string $name, int $length, mixed $defaultValue = null, bool|null $isNull = null):static{
		return $this->field(name: $name, type: 'VARCHAR', length: $length, isNull: $isNull, defaultValue: $defaultValue);
	}

	public function decimal(string $name, string $length, mixed $defaultValue = null, bool|null $isNull = null):static{
		return $this->field(name: $name, type: 'DECIMAL', length: $length, isNull: $isNull, defaultValue: $defaultValue);
	}

	public function tinytext(string $name, mixed $defaultValue = null, bool|null $isNull = null):static{
		return $this->field(name: $name, type: 'TINYTEXT', isNull: $isNull, defaultValue: $defaultValue);
	}

	public function text(string $name, mixed $defaultValue = null, bool|null $isNull = null):static{
		return $this->field(name: $name, type: 'TEXT', isNull: $isNull, defaultValue: $defaultValue);
	}

	public function mediumtext(string $name, mixed $defaultValue = null, bool|null $isNull = null):static{
		return $this->field(name: $name, type: 'MEDIUMTEXT', isNull: $isNull, defaultValue: $defaultValue);
	}

	public function longtext(string $name, mixed $defaultValue = null, bool|null $isNull = null):static{
		return $this->field(name: $name, type: 'LONGTEXT', isNull: $isNull, defaultValue: $defaultValue);
	}

}
