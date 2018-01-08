<?php
/**
 * Class MSSqlSrvQuery
 *
 * @filesource   MSSqlSrvQuery
 * @created      29.06.2017
 * @package      chillerlan\Database\Query\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use chillerlan\Database\Query\Statements\{
	Create, CreateAbstract,
	CreateDatabase, CreateDatabaseAbstract,
	CreateTable, CreateTableAbstract,
	Select, SelectAbstract,
	StatementException
};

class MSSqlSrvQuery extends QueryBuilderAbstract{

	/**
	 * @inheritdoc
	 */
	protected $quotes = ['[', ']'];

	/**
	 * @inheritdoc
	 */
	public function select():Select{

		/**
		 * @link https://msdn.microsoft.com/library/ms176104(v=sql.110).aspx
		 */
		return new class($this->db, $this->options, $this->quotes) extends SelectAbstract{

			/**
			 * @inheritdoc
			 */
			public function sql():string{

				if(empty($this->from)){
					throw new StatementException('no FROM expression specified');
				}

				$glue = ','.PHP_EOL."\t";

				$sql  = 'SELECT ';
				$sql .= $this->distinct ? 'DISTINCT ' : '';
				$sql .= !empty($this->cols) ? implode($glue , $this->cols).PHP_EOL : '* ';
				$sql .= 'FROM '.implode($glue , $this->from);
				$sql .= $this->_getWhere();
				$sql .= !empty($this->groupby) ? PHP_EOL.'GROUP BY '.implode($glue, $this->groupby) : '';
				$sql .= !empty($this->orderby) ? PHP_EOL.'ORDER BY '.implode($glue, $this->orderby) : '';

				if(!is_null($this->limit)){

					if(is_null($this->offset)){
						$this->offset = 0;
					}

					if(empty($this->orderby)){
						$sql .= PHP_EOL.'ORDER BY 1';
					}

					$sql .= PHP_EOL.'OFFSET ? ROWS FETCH NEXT ? ROWS ONLY';

				}


				return $sql;
			}

		};

	}

	/**
	 * @inheritdoc
	 */
	public function create():Create{

		return new class($this->db, $this->options, $this->quotes) extends CreateAbstract{

			/**
			 * @inheritdoc
			 */
			public function database(string $dbname):CreateDatabase{

				/**
				 * @link https://msdn.microsoft.com/library/ms176061(v=sql.110).aspx
				 */
				return (new class($this->db, $this->options, $this->quotes) extends CreateDatabaseAbstract{

					/**
					 * @inheritdoc
					 */
					public function sql():string{

						$sql = 'CREATE DATABASE ';
						$sql .= $this->quote($this->name);
						$sql .= $this->charset ? ' COLLATE '.$this->charset : '';

						return $sql;
					}

				})->name($dbname); // new class end

			}

			/**
			 * @inheritdoc
			 */
			public function table(string $tablename):CreateTable{

				/**
				 * @link https://msdn.microsoft.com/library/ms174979(v=sql.110).aspx
				 * @link https://docs.microsoft.com/sql/t-sql/data-types/data-types-transact-sql
				 */
				return (new class($this->db, $this->options, $this->quotes) extends CreateTableAbstract{

					/**
					 * @inheritdoc
					 */
					public function sql():string{

						$sql = /** @lang text */ 'CREATE TABLE ';
						$sql .= $this->quote($this->name);

						if(!empty($this->cols)){
							$sql .= ' ('.PHP_EOL."\t".implode(','.PHP_EOL."\t", $this->cols);

							if($this->primaryKey){
								$sql .=','.PHP_EOL."\t".'PRIMARY KEY ('.$this->quote($this->primaryKey).')';
							}

							$sql .= PHP_EOL.')';
						}

						return $sql;
					}

					/**
					 * @inheritdoc
					 */
					protected function fieldspec(string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null):string {
						$name = trim($name);
						$type = strtolower(trim($type));

						$field = [$this->quote($name)];

						$type_translation = [
							'boolean'    => 'tinyint',
							'bool   '    => 'tinyint',
							'mediumint'  => 'int',
							'double'     => 'float',
							'tinytext'   => 'text',
							'mediumtext' => 'text',
							'longtext'   => 'text',
							'timestamp'  => 'datetime2',
						][$type] ?? $type;

						if((is_int($length) || is_string($length) && (count(explode(',', $length)) === 2 || $length === 'max'))
						   && in_array($type, ['char', 'varchar', 'nchar', 'nvarchar', 'decimal', 'numeric', 'datetime2', 'time'], true)){
							$field[] = $type_translation.'('. $length . ')';
						}
						else{
							$field[] = $type_translation;
						}

						if(is_bool($isNull)){
							$field[] = $isNull ? 'NULL' : 'NOT NULL';
						}

						$defaultType = strtoupper($defaultType);

						if($defaultType === 'USER_DEFINED'){

							// @todo
							switch(true){
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
