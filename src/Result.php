<?php
/**
 * Class Result
 *
 * @filesource   Result.php
 * @created      28.06.2017
 * @package      chillerlan\Database
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database;

use chillerlan\Traits\{
	Enumerable, Interfaces\ArrayAccessTrait, Magic, SPL\CountableTrait, SPL\SeekableIteratorTrait
};
use ArrayAccess, Countable, SeekableIterator;

/**
 * @property int                              $length
 * @property \chillerlan\Database\ResultRow[] $array
 */
class Result implements ResultInterface, SeekableIterator, ArrayAccess, Countable{
	use ArrayAccessTrait, SeekableIteratorTrait, CountableTrait, Magic, Enumerable{
		__toArray as __EnumerableToArray;
	}

	/**
	 * @var null|string
	 */
	protected $sourceEncoding;

	/**
	 * @var string
	 */
	protected $destEncoding;

	/**
	 * @todo
	 * @var bool
	 */
	protected $isBool;

	/**
	 * @todo
	 * @var bool
	 */
	protected $success = false;

	/**
	 * @todo
	 * @var array
	 */
	protected $metadata = [];

	/** @inheritdoc */
	public function __construct(iterable $data = null, string $sourceEncoding = null, string $destEncoding = null){
		$this->sourceEncoding = $sourceEncoding;
		$this->destEncoding   = $destEncoding ?? 'UTF-8';

		if($data !== null){

			foreach($data as $k => $v){
				$this->offsetSet($k, $v);
			}

		}

		$this->offset = 0;
	}

	/** @inheritdoc */
	public function __toJSON(bool $prettyprint = null):string{
		return json_encode($this->__toArray(), $prettyprint === true ? JSON_PRETTY_PRINT : null);
	}

	/** @inheritdoc */
	public function __merge(Result $DBResult):Result{
		$this->array = array_merge($this->array, $DBResult->__EnumerableToArray());

		return $this;
	}

	/** @inheritdoc */
	public function __chunk(int $size):array{
		return array_chunk($this->__toArray(), $size, true);
	}

	/** @inheritdoc */
	public function __toArray():array{
		$arr = [];

		foreach($this->array as $key => $item){
			$arr[$key] = $item->__toArray();
		}

		return $arr;
	}

	/**
	 * @return array
	 */
	public function __fields():array{
		return array_keys($this->array);
	}

	/**
	 * @param bool|null $to_array
	 *
	 * @return array
	 */
	public function __values(bool $to_array = null):array{
		return array_values($to_array === true ? $this->__toArray() : $this->array);
	}

	/**
	 * @param string      $column
	 * @param string|null $index_key
	 *
	 * @return array
	 */
	public function __column(string $column, string $index_key = null):array{
		return array_column($this->__toArray(), $column, $index_key);
	}

	/*********
	 * magic *
	 *********/

	/**
	 * @return int
	 */
	protected function magic_get_length():int{
		return $this->count();
	}


	/***************
	 * ArrayAccess *
	 ***************/

	/** @inheritdoc */
	public function offsetSet($offset, $value):void{

		$row = new ResultRow($value, $this->sourceEncoding, $this->destEncoding);

		$offset !== null
			? $this->array[$offset] = $row
			: $this->array[] = $row;
	}

}
