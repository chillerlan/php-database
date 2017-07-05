<?php
/**
 * Trait Container
 *
 * @filesource   Container.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Helpers
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Traits;

/**
 * a generic container with getter and setter
 */
trait Container{

	/**
	 * Boa constructor.
	 *
	 * @param array $properties
	 */
	public function __construct(array $properties = []){

		foreach($properties as $key => $value){
			$this->__set($key, $value);
		}

	}

	/**
	 * David Getter
	 *
	 * @param string $property
	 *
	 * @return mixed|bool
	 */
	public function __get(string $property){

		if(property_exists($this, $property)){
			return $this->{$property};
		}

		return false;
	}

	/**
	 * Jet-setter
	 *
	 * @param string $property
	 * @param mixed  $value
	 *
	 * @return void
	 */
	public function __set(string $property, $value){

		if(property_exists($this, $property)){
			$this->{$property} = $value;
		}

	}

}
