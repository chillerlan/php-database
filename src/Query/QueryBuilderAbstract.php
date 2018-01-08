<?php
/**
 * Class QueryBuilderAbstract
 *
 * @filesource   QueryBuilderAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use chillerlan\Database\{
	DatabaseOptions, Drivers\DriverInterface
};
use chillerlan\Database\Query\Statements\{
	Alter, AlterAbstract,
	Delete, DeleteAbstract,
	Drop, DropAbstract,
	Insert, InsertAbstract,
	Update, UpdateAbstract
};
use chillerlan\Logger\LogTrait;

abstract class QueryBuilderAbstract implements QueryBuilderInterface{
	use LogTrait;

	/**
	 * @var \chillerlan\Database\Drivers\DriverInterface
	 */
	protected $db;

	/**
	 * @var \chillerlan\Database\DatabaseOptions
	 */
	protected $options;

	/**
	 * @var string[]
	 */
	protected $quotes;

	/**
	 * QueryBuilderAbstract constructor.
	 *
	 * @param \chillerlan\Database\Drivers\DriverInterface $db
	 * @param \chillerlan\Database\DatabaseOptions         $options
	 */
	public function __construct(DriverInterface $db, DatabaseOptions $options){
		$this->db      = $db;
		$this->options = $options;
	}

	/**
	 * @param $name
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
	 * @inheritdoc
	 */
	public function insert():Insert{

		/**
		 * @link https://www.sqlite.org/lang_insert.html
		 * @link https://msdn.microsoft.com/library/ms174335(v=sql.110).aspx
		 */
		return new class($this->db, $this->options, $this->quotes) extends InsertAbstract{};

	}

	/**
	 * @inheritdoc
	 */
	public function update():Update{

		/**
		 * @link https://www.sqlite.org/lang_update.html
		 * @link https://dev.mysql.com/doc/refman/5.7/en/update.html
		 * @link https://www.postgresql.org/docs/current/static/sql-update.html
		 * @link https://msdn.microsoft.com/library/ms177523(v=sql.110).aspx
		 */
		return new class($this->db, $this->options, $this->quotes) extends UpdateAbstract{};

	}

	/**
	 * @inheritdoc
	 */
	public function delete():Delete{

		/**
		 * @link https://www.sqlite.org/lang_delete.html
		 * @link https://dev.mysql.com/doc/refman/5.7/en/delete.html
		 * @link https://www.postgresql.org/docs/current/static/sql-delete.html
		 * @link https://msdn.microsoft.com/de-de/library/ms189835(v=sql.110).aspx
		 */
		return new class($this->db, $this->options, $this->quotes) extends DeleteAbstract{};

	}

	/**
	 * @inheritdoc
	 */
	public function alter():Alter{
		return new class($this->db, $this->options, $this->quotes) extends AlterAbstract{};
	}

	/**
	 * @inheritdoc
	 */
	public function drop():Drop{
		return new class($this->db, $this->options, $this->quotes) extends DropAbstract{};
	}

}
