<?php
/**
 * Class CreateTableAbstract
 *
 * @filesource   CreateTableAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Create
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Create;

use chillerlan\Database\Query\{
	CharsetTrait, IfNotExistsTrait, NameTrait, StatementAbstract, StatementException
};

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

	/** @inheritdoc */
	public function temp():CreateTable{
		$this->temp = true;

		return $this;
	}

	/** @inheritdoc */
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
	 * @throws \chillerlan\Database\Query\StatementException
	 */
	protected function fieldspec(
		/** @noinspection PhpUnusedParameterInspection */
		string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null
	):string{
		// TODO: Implement fieldspec() method.
		throw new StatementException(__METHOD__.' not implemented');
	}

	/** @inheritdoc */
	public function field(string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null):CreateTable{
		// i don't like this
		$defaultType  = $defaultValue !== null ? false : $isNull;
		$defaultValue = $defaultValue !== null ? 'USER_DEFINED' : null;

		$this->cols[$name] = $this->fieldspec($name, $type, $length, $attribute, $collation, $isNull, $defaultType, $defaultValue, $extra);

		return $this;
	}

	/** @inheritdoc */
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

	/** @inheritdoc */
	public function tinyint(string $name, int $length = null, $defaultValue = null, bool $isNull = null, string $attribute = null):CreateTable{
		return $this->field($name, 'TINYINT', $length, $attribute, null, $isNull, null, $defaultValue);
	}

	/** @inheritdoc */
	public function int(string $name, int $length = null, $defaultValue = null, bool $isNull = null, string $attribute = null):CreateTable{
		return $this->field($name, 'INT', $length, $attribute, null, $isNull, null, $defaultValue);
	}

	/** @inheritdoc */
	public function varchar(string $name, int $length, $defaultValue = null, bool $isNull = null):CreateTable{
		return $this->field($name, 'VARCHAR', $length, null, null, $isNull, null, $defaultValue);
	}

	/** @inheritdoc */
	public function decimal(string $name, string $length, $defaultValue = null, bool $isNull = null):CreateTable{
		return $this->field($name, 'DECIMAL', $length, null, null, $isNull, null, $defaultValue);
	}

	/** @inheritdoc */
	public function tinytext(string $name, $defaultValue = null, bool $isNull = null):CreateTable{
		return $this->field($name, 'TINYTEXT', null, null, null, $isNull, null, $defaultValue);
	}

	/** @inheritdoc */
	public function text(string $name, $defaultValue = null, bool $isNull = null):CreateTable{
		return $this->field($name, 'TEXT', null, null, null, $isNull, null, $defaultValue);
	}

}
