<?php
/**
 * Class StatementAbstract
 *
 * @filesource   StatementAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Dialects;

use chillerlan\Database\Drivers\DriverInterface;
use chillerlan\Database\Options;
use chillerlan\Database\Query\Statements\Select;
use chillerlan\Database\Query\Statements\Statement;

abstract class StatementAbstract implements Statement{

	/**
	 * @var \chillerlan\Database\Drivers\DriverInterface
	 */
	protected $db;

	/**
	 * @var \chillerlan\Database\Options
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
	 * @param \chillerlan\Database\Options                 $options
	 * @param string[]                                     $quotes
	 */
	public function __construct(DriverInterface $db, Options $options, array $quotes){
		$this->db      = $db;
		$this->options = $options;
		$this->quotes  = $quotes;
	}

	/**
	 * @return string
	 */
	public function sql():string {
		return '--NOT IMPLEMENTED';
	}

	/**
	 * @return array
	 */
	public function bindValues():array{

		if(!is_null($this->offset)){
			$this->bindValues['offset'] = $this->offset;
		}

		if(!is_null($this->limit)){
			$this->bindValues['limit'] = $this->limit;
		}

		return $this->bindValues;
	}

	/**
	 * @param string|null   $index
	 * @param array|null    $values
	 * @param callable|null $callback
	 *
	 * @return bool|\chillerlan\Database\Result
	 */
	public function execute(string $index = null, array $values = null, $callback = null){

		if($this instanceof Select && $this->cached){
			return $this->db->preparedCached($this->sql(), $this->bindValues(), $index, true, 300);
		}

		return $this->multi || !is_null($values)
			? is_callable($callback)
				? $this->db->multiCallback($this->sql(), $values, $callback)
				: $this->db->multi($this->sql(), !empty($values) ? $values : $this->bindValues())
			: $this->db->prepared($this->sql(), $this->bindValues(), $index);

	}

	/**
	 * @param string $str
	 * @todo ...
	 * @return string
	 */
	protected function quote(string $str):string{
		return $this->quotes[0].implode($this->quotes[1].'.'.$this->quotes[0], explode('.', $str)).$this->quotes[1];
	}

}
