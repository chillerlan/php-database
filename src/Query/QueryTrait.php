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

use chillerlan\Database\Dialects\{
	Firebird, MSSQL
};

/**
 * @extends    \chillerlan\Database\Query\StatementAbstract
 * @implements \chillerlan\Database\Query\Query
 *
 * @property  \chillerlan\Database\Drivers\DriverInterface $db
 * @property  \chillerlan\Database\Dialects\Dialect        $dialect
 * @property  \Psr\Log\LoggerInterface                     $logger
 */
trait QueryTrait{

	protected array $sql = [];
	protected bool $multi = false;
	protected bool $cached = false;
	protected int $ttl = 300;
	protected ?int $limit = null;
	protected ?int $offset = null;
	protected array $bindValues = [];

	/**
	 * @return array
	 */
	abstract protected function getSQL():array;

	/**
	 * @param bool|null $multi
	 *
	 * @return string
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function sql(bool $multi = null):string{
		$this->multi = $multi ?? false;

		$sql = trim(implode(' ', $this->getSQL()));

		// this should only happen on a corrupt dialect implementation
		if(empty($sql)){
			throw new QueryException('empty sql'); // @codeCoverageIgnore
		}

		return $sql;
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
	protected function addBindValue(string $key, $value):Statement{
		$this->bindValues[$key] = $value;

		return $this;
	}

	/** @inheritdoc */
	public function limit(int $limit):Statement{
		$this->limit = $limit >= 0 ? $limit : 0;

		return $this;
	}

	/** @inheritdoc */
	public function offset(int $offset):Statement{
		$this->offset = $offset >= 0 ? $offset : 0;

		return $this;
	}

	/** @inheritdoc */
	public function cached(int $ttl = null):Statement{
		$this->cached = true;

		if($ttl > 0){
			$this->ttl = $ttl;
		}

		return $this;
	}

	/** @inheritdoc */
	public function query(string $index = null){
		$sql        = $this->sql(false);
		$bindvalues = $this instanceof BindValues
			? $this->getBindValues()
			: null;

		$this->logger->debug('QueryTrait::query()', ['method' => __METHOD__, 'sql' => $sql, 'val' => $bindvalues, 'index' => $index]);

		if($this->cached && $this instanceof Select){
			return $this->db->preparedCached($sql, $bindvalues, $index, true, $this->ttl);
		}

		return $this->db->prepared($sql, $bindvalues, $index);
	}

}
