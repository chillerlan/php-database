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
	NameTrait, StatementAbstract, WhereTrait
};

abstract class DeleteAbstract extends StatementAbstract implements Delete{
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
