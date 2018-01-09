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
use ArrayAccess, Countable, SeekableIterator, stdClass, Traversable;

/**
 * @property int $length
 * @property \chillerlan\Database\ResultRow[] $array
 */
class Result implements SeekableIterator, ArrayAccess, Countable{
	use ArrayAccessTrait, SeekableIteratorTrait, CountableTrait, Magic, Enumerable;

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

	/**
	 * Result constructor.
	 *
	 * @param \Traversable|\stdClass|array|null $data
	 * @param string|null                       $sourceEncoding
	 * @param string                            $destEncoding
	 *
	 * @throws \chillerlan\Database\DatabaseException
	 */
	public function __construct($data = null, string $sourceEncoding = null, string $destEncoding = null){
		$this->sourceEncoding = $sourceEncoding;
		$this->destEncoding   = $destEncoding ?? 'UTF-8';

		if($data === null){
			$data = [];
		}
		else if($data instanceof Traversable){
			$data = iterator_to_array($data);
		}
		else if(!$data instanceof stdClass && !is_array($data)){
			throw new DatabaseException('invalid data');
		}

		foreach($data as $k => $v){
			$this->offsetSet($k, $v);
		}

		$this->offset = 0;
	}

	/**
	 * @param $offset
	 *
	 * @return bool
	 */
	public function __isset($offset):bool{
		return $this->offsetExists($offset);
	}

	/**
	 * @param $offset
	 */
	public function __unset($offset):void{
		$this->offsetUnset($offset);
	}

	/**
	 * @return string
	 */
	public function __toString():string{
		return json_encode($this->__toArray());
	}

	/**
	 * @param \chillerlan\Database\Result $DBResult
	 *
	 * @return \chillerlan\Database\Result
	 */
	public function __merge(Result $DBResult):Result{
		$arr = [];

		foreach($DBResult as $row){
			$arr[] = $row;
		}

		$this->array = array_merge($this->array, $arr);

		return $this;
	}

	/**
	 * @param int $size
	 *
	 * @return array
	 */
	public function __chunk(int $size):array{
		return array_chunk($this->__toArray(), $size, true);
	}

	/**
	 * @inheritdoc
	 */
	public function __toArray():array {
		$arr = [];

		foreach($this->array as $key => $item){
			$arr[$key] = $item->__toArray();
		}

		return $arr;
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

	/**
	 * @inheritdoc
	 */
	public function offsetSet($offset, $value):void{

		if(is_null($offset)){
			$this->array[] = new ResultRow($value, $this->sourceEncoding, $this->destEncoding);
		}
		else{
			$this->array[$offset] = new ResultRow($value, $this->sourceEncoding, $this->destEncoding);
		}

	}

}
