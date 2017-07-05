<?php
/**
 * Class MSSqlSrvQueryBuilder
 *
 * @filesource   MSSqlSrvQueryBuilder.php
 * @created      29.06.2017
 * @package      chillerlan\Database\Query\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Dialects;

use chillerlan\Database\Query\{
	QueryBuilderAbstract, QueryException
};

use chillerlan\Database\Query\Statements\{
	Alter, Create, CreateDatabase, CreateTable, Delete,
	Drop, Insert, Select, Update
};

class MSSqlSrvQueryBuilder extends QueryBuilderAbstract{

	/**
	 * @inheritdoc
	 */
	public function select():Select{
		return new class($this->db, $this->options, $this->quotes) extends SelectAbstract{};
	}

	/**
	 * @inheritdoc
	 */
	public function insert():Insert{
		return new class($this->db, $this->options, $this->quotes) extends InsertAbstract{};
	}

	/**
	 * @inheritdoc
	 */
	public function update():Update{
		return new class($this->db, $this->options, $this->quotes) extends UpdateAbstract{};
	}

	/**
	 * @inheritdoc
	 */
	public function delete():Delete{
		return new class($this->db, $this->options, $this->quotes) extends DeleteAbstract{};
	}

	/**
	 * @inheritdoc
	 */
	public function create():Create{
		return new class($this->db, $this->options, $this->quotes) extends StatementAbstract implements Create{

			/**
			 * @inheritdoc
			 */
			public function database(string $dbname = null):CreateDatabase{
				return (new class($this->db, $this->options, $this->quotes) extends CreateDatabaseAbstract{

				})->name($dbname); // new class end
			}

			/**
			 * @inheritdoc
			 */
			public function table(string $tablename = null):CreateTable{
				return (new class($this->db, $this->options, $this->quotes) extends CreateTableAbstract{

					/**
					 * @inheritdoc
					 */
					protected function fieldspec(
						string $name,
						string $type,
						$length = null,
						string $attribute = null,
						string $collation = null,
						bool $isNull = null,
						string $defaultType = null,
						$defaultValue = null,
						string $extra = null
					){
						// TODO: Implement fieldspec() method.
					}
				})->name($tablename); // new class end
			}
		};
	}

	/**
	 * @inheritdoc
	 */
	public function alter():Alter{
		return new class($this->db, $this->options, $this->quotes) extends StatementAbstract implements Alter{};
	}

	/**
	 * @inheritdoc
	 */
	public function drop():Drop{
		return new class($this->db, $this->options, $this->quotes) extends StatementAbstract implements Drop{};
	}

}
