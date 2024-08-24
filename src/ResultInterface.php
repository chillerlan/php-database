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

use ArrayAccess, Countable, JsonSerializable, SeekableIterator;

interface ResultInterface extends SeekableIterator, ArrayAccess, Countable, JsonSerializable{

	/** */
	public function isBool():bool;

	/** */
	public function isSuccess():bool;

	/** */
	public function toArray():array;

	/** @return mixed */
	public function each(callable $callback);

	/** */
	public function map(callable $callback):array;

	/** */
	public function reverse():ResultInterface;

	/** @return mixed */
	public function first():mixed;

	/** @return mixed */
	public function last():mixed;

	/** */
	public function clear():ResultInterface;

	/** */
	public function inspect():string;

	/** */
	public function findAll(callable $callback):array;

	/** */
	public function reject(callable $callback):array;

	/** */
	public function merge(ResultInterface $Result):ResultInterface;

	/** */
	public function chunk(int $size):array;

	/** */
	public function fields():array;

	/** */
	public function values(bool|null $to_array = null):array;

	/** */
	public function column(string $column, string|null $index_key = null):array;

}
