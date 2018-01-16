<?php
/**
 * Interface ResultInterface
 *
 * @filesource   ResultInterface.php
 * @created      13.01.2018
 * @package      chillerlan\Database
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database;

use Closure;

interface ResultInterface{

	/**
	 * Result constructor.
	 *
	 * @param iterable|null $data
	 * @param string|null   $sourceEncoding
	 * @param string        $destEncoding
	 */
	public function __construct(iterable $data = null, string $sourceEncoding = null, string $destEncoding = null);

	/**
	 * @return string
	 */
	public function __toJSON(bool $prettyprint = null):string;

	/**
	 * @param \chillerlan\Database\Result $DBResult
	 *
	 * @return \chillerlan\Database\Result
	 */
	public function __merge(Result $DBResult):Result;

	/**
	 * @param int $size
	 *
	 * @return array
	 */
	public function __chunk(int $size):array;


	/** @todo -> EnumerableInterface */

	public function __toArray():array;
	public function __each(callable $callback);
	public function __map(callable $callback):array;
	public function __reverse();
	public function __last();
	public function __clear();
	public function __inspect():string;
	public function __findAll(Closure $callback):array;
	public function __reject(Closure $callback):array;
	public function __equal(array $y):bool;

}
