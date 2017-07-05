<?php
/**
 * Class CreateDatabaseAbstract
 *
 * @filesource   CreateDatabaseAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Dialects;

use chillerlan\Database\Query\Statements\CreateDatabase;

abstract class CreateDatabaseAbstract extends StatementAbstract implements CreateDatabase{

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var bool
	 */
	protected $ifNotExists = false;

	/**
	 * @var string
	 */
	protected $collate = 'utf8';

	/**
	 * @return \chillerlan\Database\Query\Statements\CreateDatabase
	 */
	public function ifNotExists():CreateDatabase{
		$this->ifNotExists = true;

		return $this;
	}

	/**
	 * @param string|null $dbname
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateDatabase
	 */
	public function name(string $dbname = null):CreateDatabase{
		$name = trim($dbname);

		if(!empty($name)){
			$this->name = $name;
		}

		return $this;
	}

	/**
	 * @param string $collation
	 *
	 * @return \chillerlan\Database\Query\Statements\CreateDatabase
	 */
	public function charset(string $collation):CreateDatabase{
		$collation = trim($collation);

		if(!empty($collation)){
			$this->collate = $collation;
		}

		return $this;
	}

}
