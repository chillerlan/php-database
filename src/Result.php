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

use OutOfBoundsException;

use function array_chunk, array_column, array_key_exists, array_keys, array_merge, array_reverse,
	array_values, call_user_func_array, count, is_callable, method_exists, next, print_r;

/**
 * @property int                              $length
 * @property \chillerlan\Database\ResultRow[] $array
 */
class Result implements ResultInterface{

	protected ?string $sourceEncoding = null;
	protected string $destEncoding;
	protected array $array = [];
	protected int $offset = 0;

	/** @todo */
#	protected bool $isBool;
#	protected bool $success = false;
#	protected array $metadata = [];

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

	/**
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/toArray/
	 */
	public function __EnumerableToArray():array {
		return $this->array;
	}

	/**
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/each/
	 *
	 * @param callable $callback
	 */
	public function __each($callback):ResultInterface{
		$this->__map($callback);

		return $this;
	}

	/**
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/collect/
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/map/
	 *
	 * @param callable $callback
	 *
	 * @throws \chillerlan\Database\DatabaseException
	 */
	public function __map($callback):array{

		if(!is_callable($callback)){
			throw new DatabaseException('invalid callback');
		}

		$return = [];

		foreach($this->array as $index => $element){
			$return[$index] = call_user_func_array($callback, [$element, $index]);
		}

		return $return;
	}

	/**
	 * @link http://api.prototypejs.org/language/Array/prototype/reverse/
	 */
	public function __reverse():ResultInterface{
		$this->array  = array_reverse($this->array);
		$this->offset = 0;

		return $this;
	}

	/** @inheritDoc */
	public function __first(){
		return $this->array[0] ?? null;
	}

	/** @inheritDoc */
	public function __last(){
		return $this->array[count($this->array) - 1] ?? null;
	}

	/** @inheritDoc */
	public function __clear():ResultInterface{
		$this->array = [];

		return $this;
	}

	/**
	 * @link http://api.prototypejs.org/language/Array/prototype/inspect/
	 */
	public function __inspect():string {
		return print_r($this->array, true);
	}

	/**
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/findAll/
	 *
	 * @param callable $callback
	 *
	 * @throws \chillerlan\Database\DatabaseException
	 */
	public function __findAll($callback):array{

		if(!is_callable($callback)){
			throw new DatabaseException('invalid callback');
		}

		$return = [];

		foreach($this->array as $index => $element){

			if(call_user_func_array($callback, [$element, $index]) === true){
				$return[] = $element;
			}

		}

		return $return;
	}

	/**
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/reject/
	 *
	 * @param callable $callback
	 *
	 * @return array
	 * @throws \chillerlan\Database\DatabaseException
	 */
	public function __reject($callback):array{

		if(!is_callable($callback)){
			throw new DatabaseException('invalid callback');
		}

		$return = [];

		foreach($this->array as $index => $element){

			if(call_user_func_array($callback, [$element, $index]) !== true){
				$return[] = $element;
			}

		}

		return $return;
	}

	/**
	 * @return mixed|null
	 */
	public function __get(string $name){
		return $this->get($name);
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 */
	public function __set(string $name, $value):void{
		$this->set($name, $value);
	}

	/**
	 * @return mixed|null
	 */
	private function get(string $name){
		$method = 'magic_get_'.$name;

		return method_exists($this, $method) ? $this->$method() : null;
	}

	/**  */
	private function set(string $name, $value):void{
		$method = 'magic_set_'.$name;

		if(method_exists($this, $method)){
			$this->$method($value);
		}

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
	public function offsetGet($offset){
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
	public function current(){
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
	public function key(){
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

	/** @inheritdoc */
	public function __fields():array{
		return array_keys($this->array);
	}

	/** @inheritdoc */
	public function __values(bool $to_array = null):array{
		return array_values($to_array === true ? $this->__toArray() : $this->array);
	}

	/** @inheritdoc */
	public function __column(string $column, string $index_key = null):array{
		return array_column($this->__toArray(), $column, $index_key);
	}

	/*********
	 * magic *
	 *********/

	/**  */
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

	/********************
	 * JsonSerializable *
	 ********************/

	/** @inheritdoc */
	public function jsonSerialize(){
		return $this->__toArray();
	}

}
