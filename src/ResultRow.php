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
		$value = $this->array[$name] ?? null;

		if($value !== null){
			$func = $arguments[0] ?? null;

			if(is_callable($func)){
				return call_user_func_array($func, [$value]);
			}

		}

		return $value;
	}

	/**
	 * @inheritdoc
	 */
	public function __get(string $name){
		return $this->array[$name] ?? null;
	}

	/**
	 * @inheritdoc
	 */
	public function __toArray():array{
		return $this->__EnumerableToArray();
	}

	/**
	 * @inheritdoc
	 */
	public function offsetSet($offset, $value):void{

		if($this->sourceEncoding !== null && is_string($value)){
			$value = mb_convert_encoding($value, $this->destEncoding, $this->sourceEncoding);
		}

		$offset !== null
			? $this->array[$offset] = $value
			: $this->array[] = $value;

	}

}
