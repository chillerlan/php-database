<?php
/**
 * Class DropDatabaseAbstract
 *
 * @filesource   DropDatabaseAbstract.php
 * @created      07.01.2018
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

abstract class DropDatabaseAbstract extends StatementAbstract implements DropDatabase{
	use IfExistsTrait, NameTrait;

	/**
	 * @inheritdoc
	 */
	public function sql():string{

		$sql = 'DROP DATABASE ';
		$sql .= $this->ifExists ? 'IF EXISTS ' : '';
		$sql .= $this->quote($this->name);

		return $sql;
	}

	/**
	 * @param string $dbname
	 *
	 * @return \chillerlan\Database\Query\Statements\DropDatabase
	 */
	public function name(string $dbname):DropDatabase{
		return $this->_name($dbname);
	}

	/**
	 * @return \chillerlan\Database\Query\Statements\DropDatabase
	 */
	public function ifExists():DropDatabase{
		return $this->_ifExists();
	}

}