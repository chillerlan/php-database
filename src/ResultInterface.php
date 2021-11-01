<?php
/**
 * Interface ResultInterface
 *
 * @created      13.01.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database;

use ArrayAccess,Countable,SeekableIterator;

interface ResultInterface extends SeekableIterator, ArrayAccess, Countable{

	/** */
	public function __toArray():array;

	/**
	 * @param callable $callback
	 *
	 * @return mixed
	 */
	public function __each($callback);

	/**
	 * @param callable $callback
	 *
	 * @return array
	 */
	public function __map($callback):array;

	/** */
	public function __reverse():ResultInterface;

	/**
	 * @return mixed
	 */
	public function __first();

	/**
	 * @return mixed
	 */
	public function __last();

	/** */
	public function __clear():ResultInterface;

	/** */
	public function __inspect():string;

	/**
	 * @param callable $callback
	 *
	 * @return array
	 */
	public function __findAll($callback):array;

	/**
	 * @param callable $callback
	 *
	 * @return array
	 */
	public function __reject($callback):array;

	/**
	 * Result constructor.
	 */
	public function __construct(iterable $data = null, string $sourceEncoding = null, string $destEncoding = null);

	/** */
	public function __toJSON(bool $prettyprint = null):string;

	/** */
	public function __merge(Result $DBResult):Result;

	/** */
	public function __chunk(int $size):array;

	/** */
	public function __fields():array;

	/** */
	public function __values(bool $to_array = null):array;

	/** */
	public function __column(string $column, string $index_key = null):array;

}
