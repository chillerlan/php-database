<?php
/**
 * Class DropDatabaseAbstract
 *
 * @filesource   DropDatabaseAbstract.php
 * @created      07.01.2018
 * @package      chillerlan\Database\Query\Drop
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Drop;

use chillerlan\Database\Query\{
	IfExistsTrait, NameTrait, StatementAbstract
};

abstract class DropDatabaseAbstract extends StatementAbstract implements DropDatabase{
	use IfExistsTrait, NameTrait;

	/** @inheritdoc */
	public function sql():string{

		$sql = 'DROP DATABASE ';
		$sql .= $this->ifExists ? 'IF EXISTS ' : '';
		$sql .= $this->quote($this->name);

		return $sql;
	}

}