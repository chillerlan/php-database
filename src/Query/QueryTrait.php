<?php
/**
 * Trait QueryTrait
 *
 * @filesource   QueryTrait.php
 * @created      10.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use Closure;

/**
 * @extends    \chillerlan\Database\Query\StatementAbstract
 * @implements \chillerlan\Database\Query\Query
 *
 * @property  \chillerlan\Database\Drivers\DriverInterface $db
 * @property  \chillerlan\Database\Query\Dialect           $dialect
 */
trait QueryTrait{

	/**
	 * @var array
	 */
	protected $sql = [];

	/**
	 * @var bool
	 */
	protected $multi = false;

	/**
	 * @var bool
	 */
	protected $cached = false;

	/**
	 * @var int
	 */
	protected $limit;

	/**
	 * @var int
	 */
	protected $offset;

	/**
	 * @return array
	 */
	abstract protected function getSQL():array;

	/**
	 * @var array
	 */
	protected $bindValues = [];

	/** @inheritdoc */
	public function sql(bool $multi = null):string{
		$this->multi = $multi ?? false;

		return implode(' ',  $this->getSQL());
	}

	/** @inheritdoc */
	public function getBindValues():array{

		if($this->dialect instanceof Firebird || $this->dialect instanceof MSSQL){

			if($this->limit !== null){
				$this->bindValues = array_merge([
					'limit'  => $this->limit,
					'offset' => $this->offset ?? 0,
				], $this->bindValues);
			}

		}
		else{

			if($this->offset !== null){
				$this->bindValues['offset'] = $this->offset;
			}

			if($this->limit !== null){
				$this->bindValues['limit'] = $this->limit;
			}

		}

		return $this->bindValues;
	}

	/** @inheritdoc */
	protected function addBindValue(string $key, $value){
		$this->bindValues[$key] = $value;

		return $this;
	}

	/** @inheritdoc */
	public function limit(int $limit){
		$this->limit = $limit >= 0 ? $limit : 0;

		return $this;
	}

	/** @inheritdoc */
	public function offset(int $offset){
		$this->offset = $offset >= 0 ? $offset : 0;

		return $this;
	}

	/** @inheritdoc */
	public function cached(int $ttl = null){ // @todo: ttl
		$this->cached = true;

		return $this;
	}

	/** @inheritdoc */
	public function query(string $index = null){
		$sql        = $this->sql(false);
		$bindvalues = $this instanceof BindValues
			? $this->getBindValues()
			: null;

		if($this instanceof Select && $this->cached){
			return $this->db->preparedCached($sql, $bindvalues, $index, true, 300);
		}

		return $this->db->prepared($sql, $bindvalues, $index);
	}

}
