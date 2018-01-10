<?php
/**
 * Class DeleteAbstract
 *
 * @filesource   DeleteAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Delete
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Delete;

use chillerlan\Database\Query\{
	NameTrait, StatementAbstract, Where, WhereTrait
};

/**
 * @method \chillerlan\Database\Query\Delete\Delete where($val1, $val2, string $operator = null, bool $bind = null, string $join = null)
 * @method \chillerlan\Database\Query\Delete\Delete openBracket(string $join = null)
 * @method \chillerlan\Database\Query\Delete\Delete closeBracket()
 * @method \chillerlan\Database\Query\Delete\Delete from(string $name)
 */
abstract class DeleteAbstract extends StatementAbstract implements Delete, Where{
	use WhereTrait, NameTrait{
		name as from;
	}

	/** @inheritdoc */
	public function sql():string{

		$sql = 'DELETE ';
		$sql .= 'FROM '.$this->quote($this->name);
		$sql .= $this->_getWhere();

		return $sql;
	}

}
