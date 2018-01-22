<?php
/**
 * Class QueryBuilder
 *
 * @filesource   QueryBuilder.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use chillerlan\Database\{
	Drivers\DriverInterface, ResultInterface
};
use chillerlan\Logger\LogTrait;
use Psr\Log\{
	LoggerAwareInterface, LoggerInterface
};

/**
 * one big ugly block of code
 *
 * @property \chillerlan\Database\Query\Alter    $alter
 * @property \chillerlan\Database\Query\Create   $create
 * @property \chillerlan\Database\Query\Delete   $delete
 * @property \chillerlan\Database\Query\Drop     $drop
 * @property \chillerlan\Database\Query\Insert   $insert
 * @property \chillerlan\Database\Query\Select   $select
 * @property \chillerlan\Database\Query\Show     $show
 * @property \chillerlan\Database\Query\Truncate $truncate
 * @property \chillerlan\Database\Query\Update   $update
 *
 * @method setLogger(\Psr\Log\LoggerInterface $logger):QueryBuilder
 */
class QueryBuilder implements LoggerAwareInterface{
	use LogTrait;

	protected const STATEMENTS = ['alter', 'create', 'delete', 'drop', 'insert', 'select', 'show', 'truncate', 'update'];

	/**
	 * @var \chillerlan\Database\Drivers\DriverInterface
	 */
	protected $db;
	/**
	 * @var \chillerlan\Database\Dialects\Dialect
	 */
	protected $dialect;

	/**
	 * QueryBuilder constructor.
	 *
	 * @param \chillerlan\Database\Drivers\DriverInterface $db
	 * @param \Psr\Log\LoggerInterface|null                $logger
	 */
	public function __construct(DriverInterface $db, LoggerInterface $logger = null){
		$this->db      = $db;
		$this->log     = $logger;
		$this->dialect = $this->db->getDialect();
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function __get(string $name){
		$name = strtolower($name);

		if(in_array($name, $this::STATEMENTS, true)){
			return call_user_func([$this, $name]);
		}

		throw new QueryException('invalid statement');
	}

	/**
	 * @return \chillerlan\Database\Query\Alter
	 */
	public function alter():Alter{
		return new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements Alter{

			/** @inheritdoc */
			public function table(string $tablename):AlterTable{
				return (new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements AlterTable{
					use NameTrait;

				})->name($tablename);
			}

			/** @inheritdoc */
			public function database(string $dbname):AlterDatabase{
				return (new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements AlterDatabase{
					use NameTrait;

				})->name($dbname);
			}

		};
	}

	/**
	 * @return \chillerlan\Database\Query\Create
	 */
	public function create():Create{
		return new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements Create{

			/** @inheritdoc */
			public function database(string $dbname):CreateDatabase{
				return (new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements CreateDatabase, Query{
					use CharsetTrait, IfNotExistsTrait, NameTrait, QueryTrait;

					/** @inheritdoc */
					protected function getSQL():array{
						return $this->dialect->createDatabase($this->name, $this->ifNotExists, $this->charset);
					}

				})->name($dbname);
			}

			/** @inheritdoc */
			public function table(string $tablename):CreateTable{
				return (new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements CreateTable, Query{
					use CharsetTrait, IfNotExistsTrait, NameTrait, QueryTrait;

					/** @var bool */
					protected $temp = false;

					/** @var string */
					protected $primaryKey;

					/*** @var array */
					protected $cols = [];

					/*** @var string */
					protected $dir;

					/** @inheritdoc */
					protected function getSQL():array{
						return $this->dialect->createTable($this->name, $this->cols, $this->primaryKey, $this->ifNotExists, $this->temp, $this->dir);
					}

					/** @inheritdoc */
					public function temp():CreateTable{
						$this->temp = true;

						return $this;
					}

					/** @inheritdoc */
					public function primaryKey(string $field, string $dir = null):CreateTable{
						$this->primaryKey = trim($field);
						$this->dir        = strtoupper($dir);

						return $this;
					}

					/** @inheritdoc */
					public function field(string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null):CreateTable{
						$this->cols[$name] = $this->dialect->fieldspec($name, $type, $length, $attribute, $collation, $isNull, $defaultType, $defaultValue, $extra);

						return $this;
					}

					/** @inheritdoc */
					public function enum(string $name, array $values, $defaultValue = null, bool $isNull = null):CreateTable{
						$this->cols[$name] = $this->dialect->enum($name, $values, $defaultValue, $isNull);

						return $this;
					}

					/** @inheritdoc */
					public function tinyint(string $name, int $length = null, $defaultValue = null, bool $isNull = null, string $attribute = null):CreateTable{
						return $this->field($name, 'TINYINT', $length, $attribute, null, $isNull, null, $defaultValue);
					}

					/** @inheritdoc */
					public function int(string $name, int $length = null, $defaultValue = null, bool $isNull = null, string $attribute = null):CreateTable{
						return $this->field($name, 'INT', $length, $attribute, null, $isNull, null, $defaultValue);
					}

					/** @inheritdoc */
					public function varchar(string $name, int $length, $defaultValue = null, bool $isNull = null):CreateTable{
						return $this->field($name, 'VARCHAR', $length, null, null, $isNull, null, $defaultValue);
					}

					/** @inheritdoc */
					public function decimal(string $name, string $length, $defaultValue = null, bool $isNull = null):CreateTable{
						return $this->field($name, 'DECIMAL', $length, null, null, $isNull, null, $defaultValue);
					}

					/** @inheritdoc */
					public function tinytext(string $name, $defaultValue = null, bool $isNull = null):CreateTable{
						return $this->field($name, 'TINYTEXT', null, null, null, $isNull, null, $defaultValue);
					}

					/** @inheritdoc */
					public function text(string $name, $defaultValue = null, bool $isNull = null):CreateTable{
						return $this->field($name, 'TEXT', null, null, null, $isNull, null, $defaultValue);
					}

				})->name($tablename);
			}

		};
	}

	/**
	 * @return \chillerlan\Database\Query\Delete
	 */
	public function delete():Delete{
		return new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements Delete, Where, BindValues, Query{
			use WhereTrait, QueryTrait, NameTrait {
				name as from;
			}

			/** @inheritdoc */
			protected function getSQL():array{
				return $this->dialect->delete($this->name, $this->_getWhere());
			}

		};
	}

	/**
	 * @return \chillerlan\Database\Query\Drop
	 */
	public function drop():Drop{
		return new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements Drop{

			/** @inheritdoc */
			public function database(string $dbname):DropItem{
				return (new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements DropItem, Query{
					use IfExistsTrait, NameTrait, QueryTrait;

					/** @inheritdoc */
					protected function getSQL():array{
						return $this->dialect->dropDatabase($this->name, $this->ifExists);
					}

				})->name($dbname);
			}

			/** @inheritdoc */
			public function table(string $tablename):DropItem{
				return (new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements DropItem, Query{
					use IfExistsTrait, NameTrait, QueryTrait;

					/** @inheritdoc */
					protected function getSQL():array{
						return $this->dialect->dropTable($this->name, $this->ifExists);
					}

				})->name($tablename);
			}

		};
	}

	/**
	 * @return \chillerlan\Database\Query\Insert
	 */
	public function insert():Insert{
		return new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements Insert, BindValues, MultiQuery{
			use MultiQueryTrait, OnConflictTrait{
				name as into;
			}

			/** @inheritdoc */
			protected function getSQL():array{

				if(empty($this->bindValues)){
					throw new QueryException('no values given');
				}

				return $this->dialect->insert($this->name, array_keys($this->multi ? $this->bindValues[0] : $this->bindValues), $this->on_conflict);
			}

			/** @inheritdoc */
			public function values(iterable $values):Insert{

				if($values instanceof ResultInterface){
					$this->bindValues = $values->__toArray();

					return $this;
				}

				foreach($values as $key => $value){
					$this->addBindValue($key, $value);
				}

				return $this;
			}

		};
	}

	/**
	 * @return \chillerlan\Database\Query\Select
	 */
	public function select():Select{
		return new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements Select, Where, BindValues, Query{
			use QueryTrait, WhereTrait;

			/** @var bool */
			protected $distinct = false;

			/** @var array */
			protected $cols = [];

			/** @var array */
			protected $from = [];

			/** @var array */
			protected $orderby = [];

			/** @var array */
			protected $groupby = [];

			/** @inheritdoc */
			protected function getSQL():array{

				if(empty($this->from)){
					throw new QueryException('no FROM expression specified');
				}

				return $this->dialect->select($this->cols, $this->from, $this->_getWhere(), $this->limit, $this->offset, $this->distinct, $this->groupby, $this->orderby);
			}

			/** @inheritdoc */
			public function distinct():Select{
				$this->distinct = true;

				return $this;
			}

			/** @inheritdoc */
			public function cols(array $expressions):Select{
				$this->cols = $this->dialect->cols($expressions);

				return $this;
			}

			/** @inheritdoc */
			public function from(array $expressions):Select{
				$this->from = $this->dialect->from($expressions);

				return $this;
			}

			/** @inheritdoc */
			public function orderBy(array $expressions):Select{
				$this->orderby = $this->dialect->orderby($expressions);

				return $this;
			}

			/** @inheritdoc */
			public function groupBy(array $expressions):Select{

				foreach($expressions as $expression){
					$this->groupby[] = $this->dialect->quote($expression);
				}

				return $this;
			}

			/** @inheritdoc */
			public function count():int{

				if(empty($this->from)){
					throw new QueryException('no FROM expression specified');
				}

				$sql    = $this->dialect->selectCount($this->from, $this->_getWhere(), $this->distinct, $this->groupby);
				$result = $this->db->prepared(implode(' ', $sql), $this->bindValues);

				if($result instanceof ResultInterface && $result->length > 0){
					return (int)$result[0]->count;
				}

				return -1;
			}

		};
	}

	/**
	 * @return \chillerlan\Database\Query\Show
	 */
	public function show():Show{
		return new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements Show{

			/**
			 * @param string $name
			 *
			 * @return mixed
			 * @throws \chillerlan\Database\Query\QueryException
			 */
			public function __get(string $name){
				$name = strtolower($name);

				if(in_array($name, ['create'], true)){
					return call_user_func([$this, $name]);
				}

				throw new QueryException('invalid statement');
			}

			/** @inheritdoc */
			public function databases():ShowItem{
				return new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements ShowItem, Query{
					use QueryTrait;

					/** @inheritdoc */
					protected function getSQL():array{
						return $this->dialect->showDatabases(); // @todo? WHERE
					}

				};
			}

			/** @inheritdoc */
			public function tables(string $from = null):ShowItem{

				$showTables = new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements ShowItem, Where, Query{
					use QueryTrait, WhereTrait, NameTrait{
						name as from;
					}

					protected $pattern;

					/** @inheritdoc */
					protected function getSQL():array{
						return $this->dialect->showTables($this->name, $this->pattern, $this->_getWhere());
					}

					/** @inheritdoc */
					public function pattern(string $pattern):ShowItem{
						$pattern = trim($pattern);

						if(!empty($pattern)){
							$this->pattern = $pattern;
						}

						return $this;
					}

				};

				if(!empty($from)){
					$showTables->from($from);
				}

				return $showTables;
			}

			/** @inheritdoc */
			public function create():ShowCreate{
				return new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements ShowCreate{

					/** @inheritdoc */
					public function table(string $tablename):ShowItem{
						return (new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements ShowItem, Query{
							use QueryTrait, NameTrait;

							/** @inheritdoc */
							protected function getSQL():array{
								return $this->dialect->showCreateTable($this->name);
							}

						})->name($tablename);
					}

				};
			}

		};
	}

	/**
	 * @return \chillerlan\Database\Query\Truncate
	 */
	public function truncate():Truncate{
		return new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements Truncate{

			/** @inheritdoc */
			public function table(string $table):Truncate{
				return (new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements Truncate, Query{
					use QueryTrait, NameTrait {
						name as table;
					}

					/** @inheritdoc */
					protected function getSQL():array{
						return $this->dialect->truncate($this->name);
					}

				})->table($table);
			}

		};
	}

	/**
	 * @return \chillerlan\Database\Query\Update
	 */
	public function update():Update{

		return new class($this->db, $this->dialect, $this->log) extends StatementAbstract implements Update, Where, BindValues, MultiQuery{
			use WhereTrait, MultiQueryTrait, NameTrait {
				name as table;
			}

			/**
			 * @var array
			 */
			protected $set = [];

			/** @inheritdoc */
			protected function getSQL():array{

				if(empty($this->set)){
					throw new QueryException('no fields to update specified');
				}

				return $this->dialect->update($this->name, $this->set, $this->_getWhere());
			}

			/** @inheritdoc */
			public function set(array $set, bool $bind = null):Update{

				foreach($set as $k => $v){

					if($v instanceof Statement){
						$this->set[]      = $this->dialect->quote($k).' = ('.$v->sql().')';
						$this->bindValues = array_merge($this->bindValues, $v->bindValues());
					}
					elseif(is_array($v)){
						// @todo: [expr, bindval1, bindval2, ...]
					}
					else{
						if($bind === false){
							// here be dragons
							$this->set[] = is_int($k)
								? $this->dialect->quote($v).' = ?'
								: $this->dialect->quote($k).' = '.$v; //$this->db->escape($v)
						}
						else{
							$this->set[]        = $this->dialect->quote($k).' = ?';
							$this->addBindValue($k, is_bool($v) ? (int)$v : $v);// avoid errors with PDO firebird & mysql
						}
					}
				}

				return $this;
			}

		};
	}

}
