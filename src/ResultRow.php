<?php
/**
 * Class ResultRow
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database;

use Closure;
use function is_string, mb_convert_encoding;

/**
 *
 */
class ResultRow extends Result{

	public function __call(string $name, array $arguments):mixed{
		$value = $this->array[$name] ?? null;

		if($value !== null){
			$func = $arguments[0] ?? null;

			if($func instanceof Closure){
				return $func($value);
			}

		}

		return $value;
	}

	public function __get(string $name):mixed{
		return $this->array[$name] ?? null;
	}

	public function toArray():array{
		return $this->array;
	}

	public function offsetSet($offset, $value):void{

		if($this->sourceEncoding !== null && is_string($value)){
			$value = mb_convert_encoding($value, $this->destEncoding, $this->sourceEncoding);
		}

		$offset !== null
			? $this->array[$offset] = $value
			: $this->array[] = $value;

	}

}
