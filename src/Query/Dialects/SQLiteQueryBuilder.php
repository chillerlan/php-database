<?php
/**
 * Class SQLiteQueryBuilder
 *
 * @filesource   SQLiteQueryBuilder.php
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

class SQLiteQueryBuilder extends QueryBuilderAbstract{

	/**
	 * @inheritdoc
	 */
	protected $quotes = ['"', '"'];

	/**
	 * @inheritdoc
	 */
	public function select():Select{

		/**
		 * @link https://www.sqlite.org/lang_select.html
		 */
		return new class($this->db, $this->options, $this->quotes) extends SelectAbstract{

			/**
			 * @inheritdoc
			 */
			public function sql():string{

				if(empty($this->from)){
					throw new QueryException('no FROM expression specified');
				}

				$glue = ','.PHP_EOL."\t";

				$sql  = 'SELECT ';
				$sql .= $this->distinct ? 'DISTINCT ' : '';
				$sql .= !empty($this->cols) ? implode($glue , $this->cols).PHP_EOL : '* ';
				$sql .= 'FROM '.implode($glue , $this->from);
				$sql .= $this->_getWhere();
				$sql .= !empty($this->groupby) ? PHP_EOL.'GROUP BY '.implode($glue, $this->groupby) : '';
				$sql .= !empty($this->orderby) ? PHP_EOL.'ORDER BY '.implode($glue, $this->orderby) : '';
				$sql .= !is_null($this->limit) ? PHP_EOL.'LIMIT '.(!is_null($this->offset) ? '?, ?' : '?') : '';

				return $sql;
			}

		};

	}

	/**
	 * @inheritdoc
	 */
	public function insert():Insert{

		/**
		 * @link https://www.sqlite.org/lang_insert.html
		 */
		return new class($this->db, $this->options, $this->quotes) extends InsertAbstract{};

	}

	/**
	 * @inheritdoc
	 */
	public function update():Update{

		/**
		 * @link https://www.sqlite.org/lang_update.html
		 */
		return new class($this->db, $this->options, $this->quotes) extends UpdateAbstract{};

	}

	/**
	 * @inheritdoc
	 */
	public function delete():Delete{

		/**
		 * @link https://www.sqlite.org/lang_delete.html
		 */
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

				/**
				 * @link https://www.sqlite.org/quickstart.html
				 * @link https://www.sqlite.org/lang_attach.html
				 */
				return new class($this->db, $this->options, $this->quotes) extends CreateDatabaseAbstract{

					public function sql():string{
						return '--NOT SUPPORTED';
					}

				}; // new class end

			}

			/**
			 * @inheritdoc
			 */
			public function table(string $tablename = null):CreateTable{

				/**
				 * @link https://www.sqlite.org/lang_createtable.html
				 */
				return (new class($this->db, $this->options, $this->quotes) extends CreateTableAbstract{

					/**
					 * @var string
					 */
					protected $dir;

					/**
					 * @param string      $field
					 * @param string|null $dir
					 *
					 * @return \chillerlan\Database\Query\Statements\CreateTable
					 */
					public function primaryKey(string $field, string $dir = null):CreateTable{
						$this->primaryKey = $field;
						$this->dir = strtoupper($dir);

						return $this;
					}

					/**
					 * @inheritdoc
					 */
					public function sql():string{

						if(empty($this->name)){
							throw new QueryException('no name specified');
						}

						$sql = 'CREATE ';
						$sql .= $this->temp ? ' TEMPORARY ' : '';
						$sql .= 'TABLE ';
						$sql .= $this->ifNotExists ? 'IF NOT EXISTS ' : '';

						$n = explode('.', $this->name);

						$sql .= $this->quote($n[count($n)-1]);

						if(!empty($this->cols)){
							$sql .= ' ('.PHP_EOL."\t".implode(','.PHP_EOL."\t", $this->cols);

							if($this->primaryKey){
								$sql .=','.PHP_EOL."\t".'PRIMARY KEY ('.$this->quote($this->primaryKey)
								       .(in_array($this->dir, ['ASC', 'DESC']) ? $this->dir : '').')';
							}

							$sql .= PHP_EOL.')';
						}

						$sql .= '';

						return $sql;
					}

					/**
					 * @inheritdoc
					 */
					protected function fieldspec(string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null){
						$name = trim($name);
						$type = strtoupper(trim($type));
						$collation = strtoupper($collation);

						$field = ['"'.$name.'"'];

						$type_translation = [
							'MEDIUMTEXT' => 'TEXT',
							'LONGTEXT'   => 'TEXT',
						][$type] ?? $type;


						if(is_int($length)&& in_array($type, ['CHAR', 'NCHAR','VARCHAR', 'NVARCHAR', 'CHARACTER'])
						   || is_string($length) && count(explode(',', $length)) === 2 && $type === 'DECIMAL'){
							$field[] = $type_translation.'('. $length . ')';
						}
						else{
							$field[] = $type_translation;
						}

						if(is_bool($isNull)){
							$field[] = $isNull ? 'NULL' : 'NOT NULL';
						}

						if($collation && in_array($collation, ['BINARY', 'NOCASE', 'RTRIM'])){
							$field[] = 'COLLATE '.$collation;
						}


						$defaultType = strtoupper($defaultType);

						if($defaultType === 'USER_DEFINED'){
							$field[] = 'DEFAULT \''.$defaultValue.'\'';
						}
						else if(in_array($defaultType, ['CURRENT_DATE', 'CURRENT_TIME', 'CURRENT_TIMESTAMP'])){
							$field[] = 'DEFAULT '.$defaultType;
						}
						else if($defaultType === 'NULL' && $isNull === true){
							$field[] = 'DEFAULT NULL';
						}


						if($attribute){
							$field[] = $attribute;
						}

						if($extra){
							$field[] = $extra;
						}

						return implode(' ', $field);
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
