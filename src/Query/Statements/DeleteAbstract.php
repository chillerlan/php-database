<?php
/**
 * Class DeleteAbstract
 *
 * @filesource   DeleteAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

abstract class DeleteAbstract extends StatementAbstract implements Delete{
	use WhereTrait, NameTrait;

	/**
	 * @return string
	 */
	public function sql():string{

		$sql = 'DELETE ';
		$sql .= 'FROM '.$this->quote($this->name);
		$sql .= $this->_getWhere();

		return $sql;
	}

	/**
	 * @inheritdoc
	 */
	public function from(string $table):Delete{
		return $this->_name($table);
	}

	/**
	 * @inheritdoc
	 */
	public function where($val1, $val2, string $operator = null, bool $bind = null, string $join = null):Delete{
		return $this->_addWhere($val1, $val2, $operator, $bind, $join);
	}

	/**
	 * @inheritdoc
	 */
	public function openBracket(string $join = null):Delete{
		return $this->_openBracket($join); // @codeCoverageIgnore
	}

	/**
	 * @inheritdoc
	 */
	public function closeBracket():Delete{
		return $this->_closeBracket(); // @codeCoverageIgnore
	}

}
