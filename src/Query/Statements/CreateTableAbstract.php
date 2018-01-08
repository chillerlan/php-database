<?php
/**
 * Class CreateTableAbstract
 *
 * @filesource   CreateTableAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

abstract class CreateTableAbstract extends StatementAbstract implements CreateTable{
	use CharsetTrait, IfNotExistsTrait, NameTrait;

#	public function index($name):CreateTable{}
#	public function timestamp():CreateTable{}

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
	 * @param string $collation
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function charset(string $collation):CreateTable{
		return $this->_charset($collation);
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
		return $this->_ifNotExists();
	}

	/**
	 * @param string|null $tablename
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function name(string $tablename):CreateTable{
		return $this->_name($tablename);
	}

	/**
	 * @param string $field
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function primaryKey(string $field):CreateTable{
		$this->primaryKey = trim($field);

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
	 * @throws \chillerlan\Database\Query\Statements\StatementException
	 */
	protected function fieldspec(
		/** @noinspection PhpUnusedParameterInspection */
		string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null
	):string{
		// TODO: Implement fieldspec() method.
		throw new StatementException(__METHOD__.' not implemented');
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
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function field(string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null):CreateTable{
		// i don't like this
		$defaultType  = $defaultValue !== null ? false : $isNull;
		$defaultValue = $defaultValue !== null ? 'USER_DEFINED' : null;

		$this->cols[$name] = $this->fieldspec($name, $type, $length, $attribute, $collation, $isNull, $defaultType, $defaultValue, $extra);

		return $this;
	}

	/**
	 * @param string    $name
	 * @param array     $values
	 * @param null      $defaultValue
	 * @param bool|null $isNull
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function enum(string $name, array $values, $defaultValue = null, bool $isNull = null):CreateTable{

		$field = $this->quote($name);
		$field .= 'ENUM (\''.implode('\', \'', $values).'\')';

		if(is_bool($isNull)){
			$field .= $isNull ? 'NULL' : 'NOT NULL';
		}

		if(in_array($defaultValue, $values, true)){
			$field .= 'DEFAULT '.(is_int($defaultValue) || is_float($defaultValue) ? $defaultValue : '\''.$defaultValue.'\'');
		}
		elseif($isNull && strtolower($defaultValue) === 'null'){
			$field .= 'DEFAULT NULL';
		}

		$this->cols[$name] = $field;

		return $this;
	}

	/**
	 * @param string      $name
	 * @param int|null    $length
	 * @param bool|null   $isNull
	 * @param int|null    $defaultValue
	 * @param string|null $attribute
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function tinyint(string $name, int $length = null, $defaultValue = null, bool $isNull = null, string $attribute = null):CreateTable{
		return $this->field($name, 'TINYINT', $length, $attribute, null, $isNull, null, $defaultValue);
	}

	/**
	 * @param string      $name
	 * @param int|null    $length
	 * @param null        $defaultValue
	 * @param bool|null   $isNull
	 * @param null|string $attribute
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function int(string $name, int $length = null, $defaultValue = null, bool $isNull = null, string $attribute = null):CreateTable{
		return $this->field($name, 'INT', $length, $attribute, null, $isNull, null, $defaultValue);
	}

	/**
	 * @param string    $name
	 * @param int|null  $length
	 * @param bool|null $isNull
	 * @param int|null  $defaultValue
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function varchar(string $name, int $length, $defaultValue = null, bool $isNull = null):CreateTable{
		return $this->field($name, 'VARCHAR', $length, null, null, $isNull, null, $defaultValue);
	}

	/**
	 * @param string    $name
	 * @param string    $length
	 * @param bool|null $isNull
	 * @param int|null  $defaultValue
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function decimal(string $name, string $length, $defaultValue = null, bool $isNull = null):CreateTable{
		return $this->field($name, 'DECIMAL', $length, null, null, $isNull, null, $defaultValue);
	}

	/**
	 * @param string   $name
	 * @param bool     $isNull
	 * @param int|null $defaultValue
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function tinytext(string $name, $defaultValue = null, bool $isNull = null):CreateTable{
		return $this->field($name, 'TINYTEXT', null, null, null, $isNull, null, $defaultValue);
	}

	/***
	 * @param string    $name
	 * @param null      $defaultValue
	 * @param bool|null $isNull
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateTable
	 */
	public function text(string $name, $defaultValue = null, bool $isNull = null):CreateTable{
		return $this->field($name, 'TEXT', null, null, null, $isNull, null, $defaultValue);
	}

}
