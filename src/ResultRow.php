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

class ResultRow extends Result{

	/**
	 * @var mixed[]
	 */
	protected $array = [];

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
	 * @param string $name
	 *
	 * @return mixed|null
	 */
	public function __get(string $name) {

		if(isset($this->array[$name])){
			return $this->array[$name];
		}

		return null;
	}

	/**
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/toArray/
	 *
	 * @return array
	 */
	public function __toArray():array {
		return $this->array;
	}

	/**
	 * @param int   $offset
	 * @param mixed $value
	 *
	 * @return void
	 */
	public function offsetSet($offset, $value){

		$value = !is_null($this->sourceEncoding) && is_string($value)
			? mb_convert_encoding($value, $this->destEncoding, $this->sourceEncoding)
			: $value;

		if(is_null($offset)){
			$this->array[] = $value; // @codeCoverageIgnore
		}
		else{
			$this->array[$offset] = $value;
		}

	}

}
