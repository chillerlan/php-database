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
use chillerlan\Database\Query\Alter\{
	Alter, AlterAbstract
};
use chillerlan\Database\Query\Delete\{
	Delete, DeleteAbstract
};
use chillerlan\Database\Query\Drop\{
	Drop, DropAbstract
};
use chillerlan\Database\Query\Insert\{
	Insert, InsertAbstract
};
use chillerlan\Database\Query\Show\{
	Show, ShowAbstract
};
use chillerlan\Database\Query\Truncate\{
	Truncate, TruncateAbstract
};
use chillerlan\Database\Query\Update\{
	Update, UpdateAbstract
};
use chillerlan\Logger\LogTrait;
use Psr\Log\{
	LoggerAwareInterface, LoggerInterface
};


abstract class QueryBuilderAbstract implements QueryBuilderInterface, LoggerAwareInterface{
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

	/** @inheritdoc */
	public function __construct(DriverInterface $db, DatabaseOptions $options, LoggerInterface $logger = null){
		$this->db      = $db;
		$this->options = $options;
		$this->log     = $logger;
	}

	/** @inheritdoc */
	public function __get(string $name){
		$name = strtolower($name);

		if(in_array($name, $this::STATEMENTS, true)){
			return call_user_func([$this, $name]);
		}

		throw new QueryException('invalid statement');
	}

	/** @inheritdoc */
	public function alter():Alter{
		return new class($this->db, $this->options, $this->quotes) extends AlterAbstract{};
	}

	/** @inheritdoc */
	public function delete():Delete{
		return new class($this->db, $this->options, $this->quotes) extends DeleteAbstract{};
	}

	/** @inheritdoc */
	public function drop():Drop{
		return new class($this->db, $this->options, $this->quotes) extends DropAbstract{};
	}

	/** @inheritdoc */
	public function insert():Insert{
		return new class($this->db, $this->options, $this->quotes) extends InsertAbstract{};
	}

	public function show():Show{
		return new class($this->db, $this->options, $this->quotes) extends ShowAbstract{};
	}

	/** @inheritdoc */
	public function truncate():Truncate{
		return new class($this->db, $this->options, $this->quotes) extends TruncateAbstract{};
	}

	/** @inheritdoc */
	public function update():Update{
		return new class($this->db, $this->options, $this->quotes) extends UpdateAbstract{};
	}

}
