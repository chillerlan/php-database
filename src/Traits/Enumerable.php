<?php
/**
 * Trait Enumerable
 *
 * @filesource   Enumerable.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Traits
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Traits;

use chillerlan\Database\ConnectionException;

/**
 * @link http://api.prototypejs.org/language/Enumerable/
 */
trait Enumerable{

	/**
	 * @var array
	 */
	protected $array = [];

	/**
	 * @var int
	 */
	protected $offset = 0;

	/*************
	 * prototype *
	 *************/

	/**
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/toArray/
	 *
	 * @return array
	 *
	 * @codeCoverageIgnore
	 */
	public function __toArray():array {
		return $this->array;
	}

	/**
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/each/
	 *
	 * @param callable $callback
	 *
	 * @return $this
	 */
	public function __each($callback){
		$this->__map($callback);

		return $this;
	}

	/**
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/collect/
	 * @link http://api.prototypejs.org/language/Enumerable/prototype/map/
	 *
	 * @param callable $callback
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function __map($callback):array {

		if(!is_callable($callback)){
			throw new ConnectionException('invalid callback');
		}

		$return = [];

		foreach($this->array as $index => $element){
			$return[$index] = call_user_func_array($callback, [$element, $index]);
		}

		return $return;
	}

	/**
	 * @link http://api.prototypejs.org/language/Array/prototype/reverse/
	 *
	 * @return $this
	 */
	public function __reverse(){
		$this->array = array_reverse($this->array);
		$this->offset = 0;

		return $this;
	}

}
