<?php
/**
 * Class DropTableAbstract
 *
 * @filesource   DropTableAbstract.php
 * @created      07.01.2018
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

abstract class DropTableAbstract extends StatementAbstract implements DropTable{
	use IfExistsTrait, NameTrait;

	/**
	 * @inheritdoc
	 */
	public function sql():string{

		$sql = 'DROP TABLE ';
		$sql .= $this->ifExists ? 'IF EXISTS ' : '';
		$sql .= $this->quote($this->name);

		return $sql;
	}

	/**
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\Statements\DropTable
	 */
	public function name(string $tablename):DropTable{
		return $this->_name($tablename);
	}

	/**
	 * @return \chillerlan\Database\Query\Statements\DropTable
	 */
	public function ifExists():DropTable{
		return $this->_ifExists();
	}

}