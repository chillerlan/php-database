<?php
/**
 * Class DriverAbstract
 *
 * @filesource   DriverAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\{Options, Result};
use Psr\SimpleCache\CacheInterface;

abstract class DriverAbstract implements DriverInterface{

	const CACHEKEY_HASH_ALGO = 'sha256';

	/**
	 * Holds the database resource object
	 *
	 * @var resource
	 */
	protected $db;

	/**
	 * Holds the settings
	 *
	 * @var \chillerlan\Database\Options
	 */
	protected $options;

	/**
	 * @var \Psr\SimpleCache\CacheInterface
	 */
	protected $cache;

	/**
	 * Constructor.
	 *
	 * @param \chillerlan\Database\Options         $options
	 * @param \Psr\SimpleCache\CacheInterface|null $cache
	 */
	public function __construct(Options $options, CacheInterface $cache = null){
		$this->options = $options;
		$this->cache   = $cache;
	}

	/**
	 * @param string      $sql
	 * @param string|null $index
	 * @param bool        $assoc
	 *
	 * @return bool|\chillerlan\Database\Result
	 */
	abstract protected function raw_query(string $sql, string $index = null, bool $assoc = true);

	/**
	 * @param string      $sql
	 * @param array       $values
	 * @param string|null $index
	 * @param bool        $assoc
	 *
	 * @return bool|\chillerlan\Database\Result
	 */
	abstract protected function prepared_query(string $sql, array $values = [], string $index = null, bool $assoc = true);

	/**
	 * @param string $sql
	 * @param array  $values
	 *
	 * @return bool
	 */
	abstract protected function multi_query(string $sql, array $values);

	/**
	 * @param string $sql
	 * @param array  $data
	 * @param        $callback
	 *
	 * @return bool
	 */
	abstract protected function multi_callback_query(string $sql, array $data, $callback);

	/**
	 * @inheritdoc
	 */
	public function getDBResource(){
		return $this->db;
	}

	/**
	 * @inheritdoc
	 */
	public function raw(string $sql, string $index = null, bool $assoc = true){

		try{
			return $this->raw_query($sql, $index, $assoc);
		}
		catch(\Exception $e){
			throw new DriverException('sql error: ['.get_called_class().'::raw()]'.$e->getMessage());
		}

	}

	/**
	 * @inheritdoc
	 */
	public function prepared(string $sql, array $values = [], string $index = null, bool $assoc = true){

		try{
			return $this->prepared_query($sql, $values, $index, $assoc);
		}
		catch(\Exception $e){
			throw new DriverException('sql error: ['.get_called_class().'::prepared()] '.$e->getMessage());
		}

	}

	/**
	 * @inheritdoc
	 * @todo: return array of results
	 */
	public function multi(string $sql, array $values){

		if(!is_array($values) || count($values) < 1 || !is_array($values[0]) || count($values[0]) < 1){
			throw new DriverException('invalid data');
		}

		try{
			return $this->multi_query($sql, $values);
		}
		catch(\Exception $e){
			throw new DriverException('sql error: ['.get_called_class().'::multi()] '.$e->getMessage());
		}

	}

	/**
	 * @inheritdoc
	 * @todo: return array of results
	 */
	public function multiCallback(string $sql, array $data, $callback){

		if(count($data) < 1){
			throw new DriverException('invalid data');
		}

		if(!is_callable($callback)){
			throw new DriverException('invalid callback');
		}

		try{
			return $this->multi_callback_query($sql, $data, $callback);
		}
		catch(\Exception $e){
			throw new DriverException('sql error: ['.get_called_class().'::multiCallback()] '.$e->getMessage());
		}

	}

	/**
	 * @inheritdoc
	 */
	public function rawCached(string $sql, string $index = null, bool $assoc = true, int $ttl = null){
		$result = $this->cacheGet($sql, [], $index);

		if(!$result){
			$result = $this->raw($sql, $index, $assoc);

			$this->cacheSet($sql, [], $index, $result, $ttl);
		}

		return $result;
	}

	/**
	 * @inheritdoc
	 */
	public function preparedCached(string $sql, array $values = [], string $index = null, bool $assoc = true, int $ttl = null){
		$result = $this->cacheGet($sql, $values, $index);

		if(!$result){
			$result = $this->prepared($sql, $values, $index, $assoc);

			$this->cacheSet($sql, $values, $index, $result, $ttl);
		}

		return $result;
	}

	/**
	 * @param             $callable
	 * @param array       $args
	 * @param string|null $index
	 * @param bool        $assoc
	 *
	 * @return bool|\chillerlan\Database\Result
	 */
	protected function getResult($callable, array $args, string $index = null, bool $assoc){
		$out = new Result(null, $this->options->convert_encoding_src, $this->options->convert_encoding_dest);
		$i   = 0;

		while($row = call_user_func_array($callable, $args)){
			$key = $assoc && !empty($index) ? $row[$index] : $i;

			$out[$key] = $row;
			$i++;
		}

		return $i === 0 ? true : $out;
	}

	/**
	 * @param string      $sql
	 * @param array|null  $values
	 * @param string|null $index
	 *
	 * @return string
	 */
	protected function cacheKey(string $sql, array $values = [], string $index = null):string{
		return hash(self::CACHEKEY_HASH_ALGO, serialize([$sql, $values, $index]));
	}

	/**
	 * @param string      $sql
	 * @param array       $values
	 * @param string|null $index
	 *
	 * @return bool|mixed
	 */
	protected function cacheGet(string $sql, array $values = [], string $index = null){

		if($this->cache){
			return $this->cache->get($this->cacheKey($sql, $values, $index));
		}

		return false; // @codeCoverageIgnore
	}

	/**
	 * @param string      $sql
	 * @param array       $values
	 * @param string|null $index
	 * @param             $response
	 * @param int|null    $ttl
	 *
	 * @return bool
	 */
	protected function cacheSet(string $sql, array $values = [], string $index = null, $response, int $ttl = null):bool{

		if($this->cache){
			return $this->cache->set($this->cacheKey($sql, $values, $index), $response, $ttl);
		}

		return false; // @codeCoverageIgnore
	}

}
