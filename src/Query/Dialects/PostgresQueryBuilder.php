<?php
/**
 * Class PostgresQueryBuilder
 *
 * @filesource   PostgresQueryBuilder.php
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
	Create, CreateDatabase, CreateTable, Insert, Select
};

class PostgresQueryBuilder extends QueryBuilderAbstract{

	/**
	 * @inheritdoc
	 */
	protected $quotes = ['"', '"'];

	/**
	 * @inheritdoc
	 */
	public function select():Select{

		/**
		 * @link https://www.postgresql.org/docs/current/static/sql-select.html
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
				$sql .= !is_null($this->offset) ? PHP_EOL.'OFFSET ?' : '';
				$sql .= !is_null($this->limit) ? PHP_EOL.'LIMIT ?' : '';

				return $sql;
			}

		};

	}

	/**
	 * @inheritdoc
	 */
	public function insert():Insert{

		/**
		 * @link https://www.postgresql.org/docs/current/static/sql-insert.html
		 */
		return new class($this->db, $this->options, $this->quotes) extends InsertAbstract{

			/**
			 * @param string      $table
			 * @param string|null $on_conflict
			 *
			 * @return \chillerlan\Database\Query\Statements\Insert
			 */
			public function into(string $table, string $on_conflict = null):Insert{
				$this->table = trim($table);

				return $this;
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
				 * @link https://www.postgresql.org/docs/current/static/sql-createdatabase.html
				 */
				return (new class($this->db, $this->options, $this->quotes) extends CreateDatabaseAbstract{

					/**
					 * @inheritdoc
					 */
					public function sql():string{

						if(empty($this->name)){
							throw new QueryException('no name specified');
						}

						$sql = 'CREATE DATABASE ';
						$sql .= $this->quote($this->name);

						if($this->collate){
							$charset = explode(',', $this->collate, 3);

							$count = count($charset);

							if($count > 0){
								$sql .= ' ENCODING \''.strtoupper($charset[0]).'\'';
							}

							if($count > 1){
								$sql .= ' LC_COLLATE=\''.$charset[1].'\'';
							}

							if($count > 2){
								$sql .= ' LC_CTYPE=\''.$charset[2].'\'';
							}

						}

						return $sql;
					}

				})->name($dbname); // new class end

			}

			/**
			 * @inheritdoc
			 */
			public function table(string $tablename = null):CreateTable{

				/**
				 * @link https://www.postgresql.org/docs/current/static/sql-createtable.html
				 * @link https://www.postgresql.org/docs/current/static/datatype.html
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
						$sql .= $this->temp ? ' TEMPORARY ' : '';
						$sql .= 'TABLE ';
						$sql .= $this->ifNotExists ? 'IF NOT EXISTS ' : '';

						$n = explode('.', $this->name);

						$sql .= $this->quote($n[count($n)-1]);

						if(!empty($this->cols)){
							$sql .= ' ('.PHP_EOL."\t".implode(','.PHP_EOL."\t", $this->cols);

							if($this->primaryKey){
								$sql .=','.PHP_EOL."\t".'PRIMARY KEY ('.$this->quote($this->primaryKey).')';
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

						$field = [$this->quote($name)];

						$type_translation = [
							'TINYINT'    => 'SMALLINT',
							'MEDIUMINT'  => 'INT',
							'DOUBLE'     => 'DOUBLE PRECISION',
							'TINYTEXT'   => 'VARCHAR(255)',
							'DATETIME'   => 'TIMESTAMP',
							'IMAGE'      => 'BLOB',
							'MEDIUMTEXT' => 'TEXT',
							'LONGTEXT'   => 'TEXT',
						][$type] ?? $type;

						if((is_int($length) || is_string($length) && count(explode(',', $length)) === 2)
						   && in_array($type, ['BIT', 'VARBIT', 'CHAR', 'VARCHAR', 'DECIMAL', 'NUMERIC', 'TIME', 'TIMESTAMP', 'INTERVAL'])){
							$field[] = $type_translation.'('. $length . ')';
						}
						else{
							$field[] = $type_translation;
						}


						if($collation && in_array($type, ['TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT', 'VARCHAR', 'CHAR'])
						   && !in_array(strtolower($collation), ['utf8'])){
							$field[] = 'COLLATE '.$collation;
						}

						if(is_bool($isNull)){
							$field[] = $isNull ? 'NULL' : 'NOT NULL';
						}


						if($attribute){
							$field[] = strtoupper($attribute);
						}

						$defaultType = strtoupper($defaultType);

						if($defaultType === 'USER_DEFINED'){

							switch(true){
								case $type === 'TIMESTAMP' && intval($defaultValue) === 0:
									$field[] = 'DEFAULT 0';
									break;
								case $type === 'BIT' || $type === 'VARBIT':
									$field[] = 'DEFAULT b\''.preg_replace('/[^01]/', '0', $defaultValue).'\'';
									break;
								case $type === 'BOOLEAN':
									$field[] = 'DEFAULT '.preg_match('/^1|T|TRUE|YES$/i', $defaultValue) ? 'TRUE' : 'FALSE';
									break;
								case strtoupper($defaultValue) === 'NULL' && $isNull === true:
									$field[] = 'DEFAULT NULL';
									break;
								default:
									$field[] = 'DEFAULT \''.$defaultValue.'\'';
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
