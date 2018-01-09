<?php
/**
 * Class StatementAbstract
 *
 * @filesource   StatementAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use chillerlan\Database\DatabaseOptions;
use chillerlan\Database\Drivers\DriverInterface;
use chillerlan\Database\Query\Insert\Insert;
use chillerlan\Database\Query\Select\Select;
use chillerlan\Database\Query\Update\Update;
use chillerlan\Logger\LogTrait;
use Closure;

abstract class StatementAbstract implements Statement{
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
	 * @var array
	 */
	protected $bindValues = [];

	/**
	 * @var int
	 */
	protected $limit;

	/**
	 * @var int
	 */
	protected $offset;

	/**
	 * @var bool
	 */
	protected $multi = false;

	/**
	 * @var bool
	 */
	protected $cached = false;

	/**
	 * StatementAbstract constructor.
	 *
	 * @param \chillerlan\Database\Drivers\DriverInterface $db
	 * @param \chillerlan\Database\DatabaseOptions         $options
	 * @param string[]                                     $quotes
	 */
	public function __construct(DriverInterface $db, DatabaseOptions $options, array $quotes){
		$this->db      = $db;
		$this->options = $options;
		$this->quotes  = $quotes;
	}

	/** @inheritdoc */
	public function sql():string {
		return '--NOT IMPLEMENTED';
	}

	/** @inheritdoc */
	public function bindValues():array{

		if(!is_null($this->offset)){
			$this->bindValues['offset'] = $this->offset;
		}

		if(!is_null($this->limit)){
			$this->bindValues['limit'] = $this->limit;
		}

		return $this->bindValues;
	}

	/** @inheritdoc */
	public function query(string $index = null){

		if($this instanceof Select && $this->cached){
			return $this->db->preparedCached($this->sql(), $this->bindValues(), $index, true, 300);
		}

		return $this->db->prepared($this->sql(), $this->bindValues(), $index);
	}

	/** @inheritdoc */
	public function multi(iterable $values = null){

		if($this instanceof Insert || $this instanceof Update){
			return $this->db->multi($this->sql(), $values ?? $this->bindValues());
		}

		throw new StatementException('INSERT or UPDATE only');
	}

	/** @inheritdoc */
	public function callback(iterable $values, Closure $callback){

		if($this instanceof Insert || $this instanceof Update){
			return $this->db->multiCallback($this->sql(), $values, $callback);
		}

		throw new StatementException('INSERT or UPDATE only');
	}

	/**
	 * @todo
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	protected function quote(string $str):string{
		return $this->quotes[0].implode($this->quotes[1].'.'.$this->quotes[0], explode('.', $str)).$this->quotes[1];
	}

}
