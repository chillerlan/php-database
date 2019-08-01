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
	Dialects\Dialect, Result
};
use chillerlan\Settings\SettingsContainerInterface;
use Psr\Log\{
	LoggerAwareInterface, LoggerAwareTrait, LoggerInterface
};
use Psr\SimpleCache\CacheInterface;

/**
 *
 */
abstract class DriverAbstract implements DriverInterface, LoggerAwareInterface{
	use LoggerAwareTrait;

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

	protected $cachekey_hash_algo;
	protected $convert_encoding_src;
	protected $convert_encoding_dest;

	/**
	 * Constructor.
	 *
	 * @param \chillerlan\Settings\SettingsContainerInterface $options
	 * @param \Psr\SimpleCache\CacheInterface|null $cache
	 * @param \Psr\Log\LoggerInterface|null        $logger
	 */
	public function __construct(SettingsContainerInterface $options, CacheInterface $cache = null, LoggerInterface $logger = null){
		$this->options = $options;
		$this->cache   = $cache;
		$this->logger  = $logger;

		// avoid unnecessary getter calls in long loops
		$this->cachekey_hash_algo    = $this->options->cachekey_hash_algo;
		$this->convert_encoding_src  = $this->options->convert_encoding_src;
		$this->convert_encoding_dest = $this->options->convert_encoding_dest;
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
	 * @param string   $sql
	 * @param array    $values
	 *
	 * @return bool
	 */
	abstract protected function multi_query(string $sql, array $values);

	/**
	 * @param string   $sql
	 * @param iterable $data
	 * @param          $callback
	 *
	 * @return bool
	 */
	abstract protected function multi_callback_query(string $sql, iterable $data, $callback);

	/**
	 * @param string $data
	 *
	 * @return string
	 */
	abstract protected function __escape(string $data):string;

	/**
	 * @inheritdoc
	 * @codeCoverageIgnore
	 */
	public function getDBResource(){
		return $this->db;
	}

	public function getDialect():Dialect{
		return new $this->dialect($this);
	}

	public function escape($data = null){

		if($data === null){
			return 'null';
		}
		elseif(is_bool($data)){
			return (int)$data;
		}
		elseif(is_numeric($data)){
			$data = $data + 0;

			if(is_int($data)){
				return intval($data);
			}
			elseif(is_float($data)){
				return floatval($data);
			}
		}

		return $this->__escape($data);
	}

	/** @inheritdoc */
	public function raw(string $sql, string $index = null, bool $assoc = null){
		$this->checkSQL($sql);
		$this->logger->debug('DriverAbstract::raw()', ['method' => __METHOD__, 'sql' => $sql, 'index' => $index, 'assoc' => $assoc]);

		try{
			return $this->raw_query($sql, $index, $assoc !== null ? $assoc : true);
		}
		catch(\Exception $e){
			$msg = 'sql error: ['.get_called_class().'::raw()] '.$e->getMessage();
			$this->logger->error($msg);

			throw new DriverException($msg);
		}

	}

	/** @inheritdoc */
	public function prepared(string $sql, array $values = null, string $index = null, bool $assoc = null){
		$this->checkSQL($sql);
		$this->logger->debug('DriverAbstract::prepared()', ['method' => __METHOD__, 'sql' => $sql, 'val' => $values, 'index' => $index, 'assoc' => $assoc]);

		try{
			return $this->prepared_query(
				$sql,
				$values !== null ? $values : [],
				$index,
				$assoc  !== null ? $assoc  : true
			);
		}
		catch(\Exception $e){
			$msg = 'sql error: ['.get_called_class().'::prepared()] '.$e->getMessage();
			$this->logger->error($msg);

			throw new DriverException($msg);
		}

	}

	/**
	 * @inheritdoc
	 * @todo: return array of results
	 */
	public function multi(string $sql, array $values){
		$this->checkSQL($sql);

		if(!is_array($values) || count($values) < 1 || !is_array($values[0]) || count($values[0]) < 1){
			throw new DriverException('invalid data');
		}

		try{
			return $this->multi_query($sql, $values);
		}
		catch(\Exception $e){
			$msg = 'sql error: ['.get_called_class().'::multi()] '.$e->getMessage();
			$this->logger->error($msg);

			throw new DriverException($msg);
		}

	}

	/**
	 * @inheritdoc
	 * @todo: return array of results
	 * @see determine callable type? http://php.net/manual/en/language.types.callable.php#118032
	 */
	public function multiCallback(string $sql, iterable $data, $callback){
		$this->checkSQL($sql);

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
			$msg = 'sql error: ['.get_called_class().'::multiCallback()] '.$e->getMessage();
			$this->logger->error($msg);

			throw new DriverException($msg);
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
	 * @param callable    $callable
	 * @param array       $args
	 * @param string|null $index
	 * @param bool        $assoc
	 *
	 * @return bool|\chillerlan\Database\Result
	 */
	protected function getResult(callable $callable, array $args, string $index = null, bool $assoc = null){
		$out = new Result(null, $this->convert_encoding_src, $this->convert_encoding_dest);
		$i   = 0;

		/** @noinspection PhpAssignmentInConditionInspection */
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
		return hash($this->cachekey_hash_algo, serialize([$sql, $values, $index]));
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

	/**
	 * @param $sql
	 *
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	protected function checkSQL(string $sql):void{

		if(empty(trim($sql))){
			throw new DriverException('sql error: empty sql');
		}

	}

}
