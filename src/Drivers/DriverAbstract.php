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

use chillerlan\Database\{
	DatabaseOptions, Query\Dialect, Result
};
use chillerlan\Logger\LogTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @method setLogger(\Psr\Log\LoggerInterface $logger):DriverInterface
 */
abstract class DriverAbstract implements DriverInterface, LoggerAwareInterface{
	use LogTrait;

	protected const CACHEKEY_HASH_ALGO = 'sha256';

	/**
	 * Holds the database resource object
	 *
	 * @var resource
	 */
	protected $db;

	/**
	 * Holds the settings
	 *
	 * @var \chillerlan\Database\DatabaseOptions
	 */
	protected $options;

	/**
	 * @var \Psr\SimpleCache\CacheInterface
	 */
	protected $cache;

	/**
	 * The dialect to use (FQCN)
	 *
	 * @var string
	 */
	protected $dialect;

	/**
	 * Constructor.
	 *
	 * @param \chillerlan\Database\DatabaseOptions $options
	 * @param \Psr\SimpleCache\CacheInterface|null $cache
	 * @param \Psr\Log\LoggerInterface|null        $logger
	 */
	public function __construct(DatabaseOptions $options, CacheInterface $cache = null, LoggerInterface $logger = null){
		$this->options = $options;
		$this->cache   = $cache;
		$this->log     = $logger;
	}

	/**
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	public function __destruct(){
		$this->disconnect();
	}

	/**
	 * @param string      $sql
	 * @param string|null $index
	 * @param bool        $assoc
	 *
	 * @return bool|\chillerlan\Database\Result
	 */
	abstract protected function raw_query(string $sql, ?string $index, ?bool $assoc);

	/**
	 * @param string      $sql
	 * @param array       $values
	 * @param string|null $index
	 * @param bool        $assoc
	 *
	 * @return bool|\chillerlan\Database\Result
	 */
	abstract protected function prepared_query(string $sql, ?array $values, ?string $index, ?bool $assoc);

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

	/** @inheritdoc */
	public function getDBResource(){
		return $this->db;
	}

	public function getDialect():Dialect{
		return new $this->dialect;
	}

	/** @inheritdoc */
	public function raw(string $sql, string $index = null, bool $assoc = null){
		$assoc = $assoc !== null ? $assoc : true;

		try{
			return $this->raw_query($sql, $index, $assoc);
		}
		catch(\Exception $e){
			throw new DriverException('sql error: ['.get_called_class().'::raw()]'.$e->getMessage());
		}

	}

	/** @inheritdoc */
	public function prepared(string $sql, array $values = null, string $index = null, bool $assoc = null){
		$values = $values !== null ? $values : [];
		$assoc  = $assoc  !== null ? $assoc  : true;

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

	/** @inheritdoc */
	public function rawCached(string $sql, string $index = null, bool $assoc = null, int $ttl = null){
		$result = $this->cacheGet($sql, [], $index);

		if(!$result){
			$result = $this->raw($sql, $index, $assoc !== null ? $assoc : true);

			$this->cacheSet($sql, $result, [], $index, $ttl);
		}

		return $result;
	}

	/** @inheritdoc */
	public function preparedCached(string $sql, array $values = null, string $index = null, bool $assoc = null, int $ttl = null){
		$result = $this->cacheGet($sql, $values, $index);

		if(!$result){
			$result = $this->prepared($sql, $values, $index, $assoc);

			$this->cacheSet($sql, $result, $values, $index, $ttl);
		}

		return $result;
	}

	/**
	 * @todo return result only, Result::$isBool, Result::$success
	 *
	 * @param             $callable
	 * @param array       $args
	 * @param string|null $index
	 * @param bool        $assoc
	 *
	 * @return bool|\chillerlan\Database\Result
	 */
	protected function getResult($callable, array $args, string $index = null, bool $assoc = null){
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
	protected function cacheKey(string $sql, array $values = null, string $index = null):string{
		return hash($this::CACHEKEY_HASH_ALGO, serialize([$sql, $values, $index]));
	}

	/**
	 * @param string      $sql
	 * @param array       $values
	 * @param string|null $index
	 *
	 * @return bool|mixed
	 */
	protected function cacheGet(string $sql, array $values = null, string $index = null){

		if($this->cache instanceof CacheInterface){
			return $this->cache->get($this->cacheKey($sql, $values, $index));
		}

		return false; // @codeCoverageIgnore
	}

	/**
	 * @param string      $sql
	 * @param             $result
	 * @param array|null  $values
	 * @param string|null $index
	 * @param int|null    $ttl
	 *
	 * @return bool
	 */
	protected function cacheSet(string $sql, $result, array $values = null, string $index = null, int $ttl = null):bool{

		if($this->cache instanceof CacheInterface){
			return $this->cache->set($this->cacheKey($sql, $values, $index), $result, $ttl);
		}

		return false; // @codeCoverageIgnore
	}

}
