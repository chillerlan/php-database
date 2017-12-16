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

use ArrayAccess, Countable, SeekableIterator;
use chillerlan\Traits\{
	Enumerable, Magic, Interfaces\ArrayAccessTrait, SPL\SeekableIteratorTrait, SPL\CountableTrait
};

/**
 * @property int $length
 *
 * each($func [, $fieldname)
 */
class Result implements SeekableIterator, ArrayAccess, Countable{
	use ArrayAccessTrait, SeekableIteratorTrait, CountableTrait, Magic, Enumerable;

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
	public function __construct($data = null, string $sourceEncoding = null, string $destEncoding = null){
		$this->sourceEncoding = $sourceEncoding;
		$this->destEncoding   = $destEncoding ?? 'UTF-8';

		if($data === null){
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

}
