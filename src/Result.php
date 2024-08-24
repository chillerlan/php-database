<?php
/**
 * Class Result
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database;

use Closure;
use OutOfBoundsException;

use function array_chunk, array_column, array_key_exists, array_keys, array_merge, array_reverse,
	array_values, count, next, print_r;

class Result implements ResultInterface{

	protected string|null $sourceEncoding = null;
	protected string  $destEncoding;
	protected bool    $isBool;
	protected bool    $isSuccess;
	/** @var  \chillerlan\Database\ResultRow[]  */
	protected array   $array          = [];
	protected int     $offset         = 0;

	/** @todo */
#	protected array $metadata = [];

	public function __construct(
		iterable|null $data = null,
		string|null $sourceEncoding = null,
		string|null $destEncoding = null,
		bool|null $isBool = null,
		bool|null $isSuccess = null
	){
		$this->sourceEncoding = $sourceEncoding;
		$this->destEncoding   = $destEncoding ?? 'UTF-8';
		$this->isBool         = $isBool ?? false;
		$this->isSuccess      = $isSuccess ?? true;

		if($data !== null){

			foreach($data as $k => $v){
				$this->offsetSet($k, $v);
			}

		}

		$this->offset = 0;
	}

	public function isBool():bool{
		return $this->isBool;
	}

	public function isSuccess():bool{
		return $this->isSuccess;
	}

	/**
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/each/
	 */
	public function each(Closure $callback):ResultInterface{
		$this->map($callback);

		return $this;
	}

	/**
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/collect/
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/map/
	 *
	 * @throws \chillerlan\Database\DatabaseException
	 */
	public function map(Closure $callback):array{
		$return = [];

		foreach($this->array as $index => $element){
			$return[$index] = $callback($element, $index);
		}

		return $return;
	}

	/**
	 * @link http://api.prototypejs.org/language/Array/prototype/reverse/
	 */
	public function reverse():ResultInterface{
		$this->array  = array_reverse($this->array);
		$this->offset = 0;

		return $this;
	}

	/** @inheritDoc */
	public function first():mixed{
		return $this->array[0] ?? null;
	}

	/** @inheritDoc */
	public function last():mixed{
		return $this->array[count($this->array) - 1] ?? null;
	}

	/** @inheritDoc */
	public function clear():ResultInterface{
		$this->array = [];

		return $this;
	}

	/**
	 * @link http://api.prototypejs.org/language/Array/prototype/inspect/
	 */
	public function inspect():string{
		return print_r($this->array, true);
	}

	/**
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/findAll/
	 *
	 * @throws \chillerlan\Database\DatabaseException
	 */
	public function findAll(Closure $callback):array{
		$return = [];

		foreach($this->array as $index => $element){

			if($callback($element, $index) === true){
				$return[] = $element;
			}

		}

		return $return;
	}

	/**
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/reject/
	 *
	 * @throws \chillerlan\Database\DatabaseException
	 */
	public function reject(Closure $callback):array{
		$return = [];

		foreach($this->array as $index => $element){

			if($callback($element, $index) !== true){
				$return[] = $element;
			}

		}

		return $return;
	}

	/**
	 * @link  http://php.net/manual/arrayaccess.offsetexists.php
	 * @inheritdoc
	 */
	public function offsetExists($offset):bool{
		return array_key_exists($offset, $this->array);
	}

	/**
	 * @link  http://php.net/manual/arrayaccess.offsetget.php
	 * @inheritdoc
	 */
	public function offsetGet($offset):mixed{
		return $this->array[$offset] ?? null;
	}

	/**
	 * @link  http://php.net/manual/arrayaccess.offsetunset.php
	 * @inheritdoc
	 */
	public function offsetUnset($offset):void{
		unset($this->array[$offset]);
	}


	/**
	 * @link http://php.net/manual/countable.count.php
	 * @inheritdoc
	 */
	public function count():int{
		return count($this->array);
	}

	/**
	 * @link  http://php.net/manual/iterator.current.php
	 * @inheritdoc
	 */
	public function current():mixed{
		return $this->array[$this->offset] ?? null;
	}

	/**
	 * @link  http://php.net/manual/iterator.next.php
	 * @inheritdoc
	 */
	public function next():void{
		$this->offset++;
	}

	/**
	 * @link  http://php.net/manual/iterator.key.php
	 * @inheritdoc
	 */
	public function key():int{
		return $this->offset;
	}

	/**
	 * @link  http://php.net/manual/iterator.valid.php
	 * @inheritdoc
	 */
	public function valid():bool{
		return array_key_exists($this->offset, $this->array);
	}

	/**
	 * @link  http://php.net/manual/iterator.rewind.php
	 * @inheritdoc
	 */
	public function rewind():void{
		$this->offset = 0;
	}

	/**
	 * @link  http://php.net/manual/seekableiterator.seek.php
	 * @inheritdoc
	 */
	public function seek($offset):void{
		$this->rewind();

		for( ; $this->offset < $offset; ){

			if(!next($this->array)) {
				throw new OutOfBoundsException('invalid seek position: '.$offset);
			}

			$this->offset++;
		}

	}

	/** @inheritdoc */
	public function merge(ResultInterface $Result):ResultInterface{
		/** @phan-suppress-next-line PhanUndeclaredProperty */
		$this->array = array_merge($this->array, $Result->array);

		return $this;
	}

	/** @inheritdoc */
	public function chunk(int $size):array{
		return array_chunk($this->toArray(), $size, true);
	}

	/** @inheritdoc */
	public function toArray():array{
		$arr = [];

		foreach($this->array as $key => $item){
			$arr[$key] = $item->toArray();
		}

		return $arr;
	}

	/** @inheritdoc */
	public function fields():array{
		return array_keys($this->array);
	}

	/** @inheritdoc */
	public function values(bool|null $to_array = null):array{
		return array_values($to_array === true ? $this->toArray() : $this->array);
	}

	/** @inheritdoc */
	public function column(string $column, string|null $index_key = null):array{
		return array_column($this->toArray(), $column, $index_key);
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

	/********************
	 * JsonSerializable *
	 ********************/

	/** @inheritdoc */
	public function jsonSerialize():mixed{
		return $this->toArray();
	}

}
