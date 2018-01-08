<?php
/**
 * Class CreateDatabaseAbstract
 *
 * @filesource   CreateDatabaseAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

abstract class CreateDatabaseAbstract extends StatementAbstract implements CreateDatabase{
	use CharsetTrait, IfNotExistsTrait, NameTrait;

	/**
	 * @var string
	 */
	protected $collate = 'utf8';

	/**
	 * @return \chillerlan\Database\Query\Statements\CreateDatabase
	 */
	public function ifNotExists():CreateDatabase{
		return $this->_ifNotExists();
	}

	/**
	 * @inheritdoc
	 */
	public function name(string $dbname):CreateDatabase{
		return $this->_name($dbname);
	}

	/**
	 * @inheritdoc
	 */
	public function charset(string $collation):CreateDatabase{
		return $this->_charset($collation);
	}

 }
