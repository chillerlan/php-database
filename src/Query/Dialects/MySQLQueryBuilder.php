<?php
/**
 * Class MySQLQueryBuilder
 *
 * @filesource   MySQLQueryBuilder.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Dialects\MySQL
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Dialects;

use chillerlan\Database\Query\{
	QueryBuilderAbstract, QueryException
};

use chillerlan\Database\Query\Statements\{
	Create, CreateDatabase, CreateTable, Insert, Select
};

class MySQLQueryBuilder extends QueryBuilderAbstract{

	/**
	 * @inheritdoc
	 */
	protected $quotes = ['`', '`'];

	/**
	 * @inheritdoc
	 */
	public function select():Select{

		/**
		 * @link https://dev.mysql.com/doc/refman/5.7/en/select.html
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
		 * @link https://dev.mysql.com/doc/refman/5.7/en/insert.html
		 */
		return new class($this->db, $this->options, $this->quotes) extends InsertAbstract{

			protected $on_conflict;

			/**
			 * @param string      $table
			 * @param string|null $on_conflict
			 *
			 * @return \chillerlan\Database\Query\Statements\Insert
			 */
			public function into(string $table, string $on_conflict = null):Insert{
				$this->table       = trim($table);
				$this->on_conflict = strtoupper($on_conflict);

				return $this;
			}

			/**
			 * @return string
			 * @throws \chillerlan\Database\Query\QueryException
			 */
			public function sql():string{

				if(empty($this->table)){
					throw new QueryException('no table specified');
				}

				if(empty($this->bindValues)){
					throw new QueryException('no values given');
				}

				$fields = $this->multi ? array_keys($this->bindValues[0]) : array_keys($this->bindValues);

				$sql  = 'INSERT ';
				$sql .= in_array($this->on_conflict, ['IGNORE']) ? $this->on_conflict.' ' : '';
				$sql .= 'INTO '.$this->quote($this->table);
				$sql .= ' ('.$this->quotes[0].implode($this->quotes[1].', '.$this->quotes[0], $fields).$this->quotes[1].')';
				$sql .= ' VALUES ('.implode(',', array_fill(0, count($fields), '?')).')';

				return $sql;
			}

		};

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
				 * @link https://dev.mysql.com/doc/refman/5.7/en/create-database.html
				 */
				return (new class($this->db, $this->options, $this->quotes) extends CreateDatabaseAbstract{

					/**
					 * @inheritdoc
					 */
					protected $collate = 'utf8mb4_bin';

					/**
					 * @inheritdoc
					 */
					public function sql():string{

						if(empty($this->name)){
							throw new QueryException('no name specified');
						}

						list($charset) = explode('_', $this->collate);

						$collate = 'CHARACTER SET '.$charset;

						if($charset !== $this->collate){
							$collate .= ' COLLATE '.$this->collate;
						}

						$sql = 'CREATE DATABASE ';
						$sql .= $this->ifNotExists ? 'IF NOT EXISTS ' : '';
						$sql .= $this->quote($this->name);
						$sql .= $this->collate ? ' '.$collate : '';

						return $sql;
					}

				})->name($dbname); // new class end

			}

			/**
			 * @inheritdoc
			 */
			public function table(string $tablename = null):CreateTable{

				/**
				 * @link https://dev.mysql.com/doc/refman/5.7/en/create-table.html
				 */
				return (new class($this->db, $this->options, $this->quotes) extends CreateTableAbstract{

					/**
					 * @inheritdoc
					 */
					public function sql():string{

						if(empty($this->name)){
							throw new QueryException('no name specified');
						}

						$sql = 'CREATE ';
						$sql .= $this->temp ? 'TEMPORARY ' : '';
						$sql .= 'TABLE ';
						$sql .= $this->ifNotExists ? 'IF NOT EXISTS ' : '';
						$sql .= $this->quote($this->name);

						if(!empty($this->cols)){
							$sql .= ' ('.PHP_EOL."\t".implode(','.PHP_EOL."\t", $this->cols);

							if($this->primaryKey){
								$sql .=','.PHP_EOL."\t".'PRIMARY KEY ('.$this->quote($this->primaryKey).')';
							}

							$sql .= PHP_EOL.')';
						}

						if(!empty($this->collate)){
							list($charset) = explode('_', $this->collate);

							$sql .= PHP_EOL.'CHARACTER SET '.$charset;

							if($charset !== $this->collate){
								$sql .= ' COLLATE '.$this->collate;
							}
						}

						return $sql;
					}

					/**
					 * @inheritdoc
					 */
					protected function fieldspec(string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null){
						$name = trim($name);
						$type = strtoupper(trim($type));

						$field = ['`'.$name.'`'];

						// @todo: whitelist types?
						$nolengthtypes = ['DATE', 'TINYBLOB', 'TINYTEXT', 'BLOB', 'TEXT', 'MEDIUMBLOB',
						                  'MEDIUMTEXT', 'LONGBLOB', 'LONGTEXT', 'SERIAL', 'BOOLEAN', 'UUID'];

						$field[] = (is_int($length) || is_string($length) && count(explode(',', $length)) === 2) && !in_array($type, $nolengthtypes)
							? $type.'('. $length . ')'
							: $type;

						if($attribute){
							$field[] = strtoupper($attribute);
						}

						$collationtypes = ['TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT', 'VARCHAR', 'CHAR', 'ENUM', 'SET'];
						if($collation && in_array($type, $collationtypes)){
							list($charset) = explode('_', $collation);

							$field[] = 'CHARACTER SET '.$charset;

							if($charset !== $collation){
								$field[] = 'COLLATE '.$collation;
							}

						}

						if(is_bool($isNull)){
							$field[] = $isNull ? 'NULL' : 'NOT NULL';
						}

						$defaultType = strtoupper($defaultType);

						if($defaultType === 'USER_DEFINED'){

							switch(true){
								case $type === 'TIMESTAMP' && intval($defaultValue) === 0:
									$field[] = 'DEFAULT 0';
									break;
								case $type === 'BIT':
									$field[] = 'DEFAULT b\''.preg_replace('/[^01]/', '0', $defaultValue).'\'';
									break;
								case $type === 'BOOLEAN':
									$field[] = 'DEFAULT '.preg_match('/^1|T|TRUE|YES$/i', $defaultValue) ? 'TRUE' : 'FALSE';
									break;
								case $type === 'BINARY' || $type === 'VARBINARY':
									$field[] = 'DEFAULT 0x'.$defaultValue;
									break;
								case strtoupper($defaultValue) === 'NULL' && $isNull === true:
									$field[] = 'DEFAULT NULL';
									break;
								default:
									$field[] = 'DEFAULT '.(is_int($defaultValue) || is_float($defaultValue) ? $defaultValue : '\''.$defaultValue.'\'') ;
							}

						}
						else if($defaultType === 'CURRENT_TIMESTAMP'){
							$field[] = 'DEFAULT CURRENT_TIMESTAMP';
						}
						else if($defaultType === 'NULL' && $isNull === true){
							$field[] = 'DEFAULT NULL';
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

}
