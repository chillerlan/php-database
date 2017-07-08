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

use ArrayAccess, Countable, Iterator;
use chillerlan\Database\Traits\{Enumerable, Magic};

/**
 * @property int $length
 *
 * each($func [, $fieldname)
 */
class Result implements Iterator, ArrayAccess, Countable{
	use Enumerable, Magic;

	/**
	 * @var \chillerlan\Database\ResultRow[]
	 */
	protected $array = [];

	/**
	 * @var int
	 */
	protected $offset = 0;

	/**
	 * @var null|string
	 */
	protected $sourceEncoding;

	/**
	 * @var string
	 */
	protected $destEncoding;

	/**
	 * Result constructor.
	 *
	 * @param \Traversable|\stdClass|array|null $data
	 * @param string|null                       $sourceEncoding
	 * @param string                            $destEncoding
	 *
	 * @throws \chillerlan\Database\ConnectionException
	 */
	public function __construct($data = null, $sourceEncoding = null, $destEncoding = 'UTF-8'){
		$this->sourceEncoding = $sourceEncoding;
		$this->destEncoding   = $destEncoding;

		if(is_null($data)){
			$data = [];
		}
		else if($data instanceof \Traversable){
			$data = iterator_to_array($data);
		}
		else if(!$data instanceof \stdClass && !is_array($data)){
			throw new ConnectionException('invalid data');
		}

		foreach($data as $k => $v){
			$this->offsetSet($k, $v);
		}

		$this->offset = 0;
	}

	/**
	 * @param \chillerlan\Database\Result $DBResult
	 *
	 * @return \chillerlan\Database\Result
	 */
	public function __merge(Result $DBResult){
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
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/toArray/
	 *
	 * @return array
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
	 * @param int|string $offset
	 *
	 * @return bool
	 */
	public function offsetExists($offset):bool{
		return isset($this->array[$offset]);
	}

	/**
	 * @param int|string $offset
	 *
	 * @return \chillerlan\Database\ResultRow|mixed|null
	 */
	public function offsetGet($offset){
		return $this->array[$offset] ?? null;
	}

	/**
	 * @param int|string   $offset
	 * @param array        $value
	 *
	 * @return void
	 */
	public function offsetSet($offset, $value){

		if(is_null($offset)){
			$this->array[] = new ResultRow($value, $this->sourceEncoding, $this->destEncoding);
		}
		else{
			$this->array[$offset] = new ResultRow($value, $this->sourceEncoding, $this->destEncoding);
		}

	}

	/**
	 * @param int|string $offset
	 *
	 * @return void
	 */
	public function offsetUnset($offset){
		unset($this->array[$offset]);
	}


	/*************
	 * Countable *
	 *************/

	/**
	 * @return int
	 */
	public function count():int{
		return count($this->array);
	}


	/************
	 * Iterator *
	 ************/

	/**
	 * @return \chillerlan\Database\ResultRow|mixed
	 */
	public function current(){
		return $this->offsetGet($this->offset);
	}

	/**
	 * @return int
	 */
	public function key():int{
		return $this->offset;
	}

	/**
	 * @return bool
	 */
	public function valid():bool{
		return $this->offsetExists($this->offset);
	}

	/**
	 *  @return void
	 */
	public function next(){
		$this->offset++;
	}

	/**
	 * @return void
	 */
	public function rewind(){
		$this->offset = 0;
	}

}
