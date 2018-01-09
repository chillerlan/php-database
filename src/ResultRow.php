<?php
/**
 * Class ResultRow
 *
 * @filesource   ResultRow.php
 * @created      28.06.2017
 * @package      chillerlan\Database
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database;

/**
 * @property mixed[] $array
 */
class ResultRow extends Result{

	/**
	 * @param string $name
	 * @param array  $arguments
	 *
	 * @return mixed|null
	 */
	public function __call(string $name, array $arguments){

		if(isset($this->array[$name])){

			if(isset($arguments[0]) && is_callable($arguments[0])){
				return call_user_func_array($arguments[0], [$this->array[$name]]);
			}

			return $this->array[$name];
		}

		return null;
	}

	/**
	 * @inheritdoc
	 */
	public function __get(string $name){

		if(isset($this->array[$name])){
			return $this->array[$name];
		}

		return null;
	}

	/**
	 * @inheritdoc
	 */
	public function __toArray():array{
		return $this->array;
	}

	/**
	 * @inheritdoc
	 */
	public function offsetSet($offset, $value):void{

		$value = !is_null($this->sourceEncoding) && is_string($value)
			? mb_convert_encoding($value, $this->destEncoding, $this->sourceEncoding)
			: $value;

		if(is_null($offset)){
			$this->array[] = $value;
		}
		else{
			$this->array[$offset] = $value;
		}

	}

}
