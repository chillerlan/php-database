<?php
/**
 * Class CreateTableAbstract
 *
 * @filesource   CreateTableAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Dialects;

use chillerlan\Database\Query\Statements\CreateTable;

abstract class CreateTableAbstract extends StatementAbstract implements CreateTable{

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var bool
	 */
	protected $ifNotExists = false;

	/**
	 * @var bool
	 */
	protected $temp = false;

	/**
	 * @var string
	 */
	protected $primaryKey;

	/**
	 * @var array
	 */
	protected $cols = [];

	/**
	 * @var string
	 */
	protected $collate;

	/**
	 * @param string $collation
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function charset(string $collation):CreateTable{
		$collation = trim($collation);

		if(!empty($collation)){
			$this->collate = $collation;
		}

		return $this;
	}

	/**
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function temp():CreateTable{
		$this->temp = true;

		return $this;
	}

	/**
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function ifNotExists():CreateTable{
		$this->ifNotExists = true;

		return $this;
	}

	/**
	 * @param string|null $tablename
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function name(string $tablename = null):CreateTable{
		$name = trim($tablename);

		if(!empty($name)){
			$this->name = $tablename;
		}

		return $this;
	}

	/**
	 * @param string $field
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function primaryKey(string $field):CreateTable{
		$this->primaryKey = $field;

		return $this;
	}

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
	 * @return mixed
	 */
	abstract protected function fieldspec(
		string $name,
		string $type,
		$length = null,
		string $attribute = null,
		string $collation = null,
		bool $isNull = null,
		string $defaultType = null,
		$defaultValue = null,
		string $extra = null
	);

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
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function field(
		string $name,
		string $type,
		$length = null,
		string $attribute = null,
		string $collation = null,
		bool $isNull = null,
		string $defaultType = null,
		$defaultValue = null,
		string $extra = null
	):CreateTable {

		$this->cols[$name] = $this->fieldspec($name, $type, $length, $attribute, $collation, $isNull, $defaultType, $defaultValue, $extra);

		return $this;
	}

	/**
	 * @param $name
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
#	public function index($name):CreateTable{}

	// @todo: clean up this mess...

	/**
	 * @param string      $name
	 * @param int|null    $length
	 * @param bool|null   $isNull
	 * @param int|null    $defaultValue
	 * @param string|null $attribute
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function tinyint(string $name, int $length = null,  $defaultValue = null , bool $isNull = null, string $attribute = null):CreateTable{
		return $this->field($name, 'TINYINT', $length, $attribute, null, !is_null($defaultValue) ? false : $isNull, !is_null($defaultValue) ? 'USER_DEFINED' : null, $defaultValue);
	}


	public function int(string $name, int $length = null,  $defaultValue = null , bool $isNull = null, string $attribute = null):CreateTable{
		return $this->field($name, 'INT', $length, $attribute, null, !is_null($defaultValue) ? false : $isNull, !is_null($defaultValue) ? 'USER_DEFINED' : null, $defaultValue);
	}


	/**
	 * @param string    $name
	 * @param int|null  $length
	 * @param bool|null $isNull
	 * @param int|null  $defaultValue
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function varchar(string $name, int $length,  $defaultValue = null , bool $isNull = null):CreateTable{
		return $this->field($name, 'VARCHAR', $length, null, null, !is_null($defaultValue) ? false : $isNull, !is_null($defaultValue) ? 'USER_DEFINED' : null, $defaultValue);
	}

	/**
	 * @param string    $name
	 * @param string    $length
	 * @param bool|null $isNull
	 * @param int|null  $defaultValue
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function decimal(string $name, string $length,  $defaultValue = null , bool $isNull = null):CreateTable{
		return $this->field($name, 'DECIMAL', $length, null, null, !is_null($defaultValue) ? false : $isNull, !is_null($defaultValue) ? 'USER_DEFINED' : null, $defaultValue);
	}

	/**
	 * @param string   $name
	 * @param bool     $isNull
	 * @param int|null $defaultValue
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function tinytext(string $name,  $defaultValue = null , bool $isNull = true):CreateTable{
		return $this->field($name, 'TINYTEXT', null, null, null, !is_null($defaultValue) ? false : $isNull, !is_null($defaultValue) ? 'USER_DEFINED' : null, $defaultValue);
	}


	public function text(string $name,  $defaultValue = null , bool $isNull = true):CreateTable{
		return $this->field($name, 'TEXT', null, null, null, !is_null($defaultValue) ? false : $isNull, !is_null($defaultValue) ? 'USER_DEFINED' : null, $defaultValue);
	}


	/**
	 * @param string    $name
	 * @param array     $values
	 * @param null      $defaultValue
	 * @param bool|null $isNull
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function enum(string $name, array $values, $defaultValue = null , bool $isNull = null):CreateTable{

		$field = $this->quote($name);
		$field .= 'ENUM (\''.implode('\', \'', $values).'\')';

		if(is_bool($isNull)){
			$field .= $isNull ? 'NULL' : 'NOT NULL';
		}

		if(in_array($defaultValue, $values, true)){
			$field .= 'DEFAULT '.(is_int($defaultValue) || is_float($defaultValue) ? $defaultValue : '\''.$defaultValue.'\'') ;
		}
		elseif($isNull && strtolower($defaultValue) === 'null'){
			$field .= 'DEFAULT NULL';
		}

		$this->cols[$name] = $field;

		return $this;
	}


	public function timestamp():CreateTable{

		return $this;
	}

}
