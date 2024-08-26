<?php
/**
 * Interface ResultInterface
 *
 * @created      13.01.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database;

use ArrayAccess, Countable, JsonSerializable, SeekableIterator;
use Closure;

interface ResultInterface extends SeekableIterator, ArrayAccess, Countable, JsonSerializable{

	/** */
	public function isBool():bool;

	/** */
	public function isSuccess():bool;

	/** */
	public function toArray():array;

	/** */
	public function each(Closure $callback);

	/** */
	public function map(Closure $callback):array;

	/** */
	public function reverse():ResultInterface;

	/** */
	public function first():mixed;

	/** */
	public function last():mixed;

	/** */
	public function clear():ResultInterface;

	/** */
	public function inspect():string;

	/** */
	public function findAll(Closure $callback):array;

	/** */
	public function reject(Closure $callback):array;

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
