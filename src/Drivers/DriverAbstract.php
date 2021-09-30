<?php
/**
 * Class DriverAbstract
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Result;
use chillerlan\Settings\SettingsContainerInterface;
use Throwable;
use Psr\Log\{LoggerAwareInterface, LoggerInterface, NullLogger};
use Psr\SimpleCache\CacheInterface;

use function bin2hex, call_user_func_array, count, floatval, get_called_class, hash, intval, is_array, is_bool,
	is_callable, is_float, is_int, is_numeric, serialize, trim;

/**
 *
 */
abstract class DriverAbstract implements DriverInterface, LoggerAwareInterface{

	/** @var \chillerlan\Database\DatabaseOptions */
	protected SettingsContainerInterface $options;
	protected LoggerInterface $logger;
	protected ?CacheInterface $cache = null;
	protected string $cachekey_hash_algo;
	protected ?string $convert_encoding_src;
	protected ?string $convert_encoding_dest;

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

		$this->setLogger($logger ?? new NullLogger);

		// avoid unnecessary getter calls in long loops
		$this->cachekey_hash_algo    = $this->options->cachekey_hash_algo;
		$this->convert_encoding_src  = $this->options->convert_encoding_src;
		$this->convert_encoding_dest = $this->options->convert_encoding_dest;
	}

	/**
	 * disconnect
	 */
	public function __destruct(){
		$this->disconnect();
	}

	/**
	 * @return bool|\chillerlan\Database\Result
	 */
	abstract protected function raw_query(string $sql, ?string $index, ?bool $assoc);

	/**
	 * @return bool|\chillerlan\Database\Result
	 */
	abstract protected function prepared_query(string $sql, ?array $values, ?string $index, ?bool $assoc);

	/**
	 *
	 */
	abstract protected function multi_query(string $sql, array $values):bool;

	/**
	 *
	 */
	abstract protected function multi_callback_query(string $sql, array $data, $callback):bool;

	/**
	 * Sets a logger.
	 */
	public function setLogger(LoggerInterface $logger):void{
		$this->logger = $logger;
	}

	/**
	 * @inheritdoc
	 */
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

		return $this->escape_string($data);
	}

	/**
	 * Escape a string by converting it to a binary hex literal instead of relying on quotes
	 *
	 * @see https://stackoverflow.com/a/12710285
	 * @see https://dev.mysql.com/doc/refman/5.7/en/hexadecimal-literals.html
	 * @see https://mariadb.com/kb/en/hexadecimal-literals/
	 * @see https://firebirdsql.org/refdocs/langrefupd25-hexbinstrings.html
	 */
	protected function escape_string(string $string):string{

		if($string === ''){
			return "''";
		}

		// convert to hex literal
		return "x'".bin2hex($string)."'";
	}

	/**
	 * @inheritdoc
	 */
	public function raw(string $sql, string $index = null, bool $assoc = null){
		$this->checkSQL($sql);

		$this->logger->debug(
			'DriverAbstract::raw()',
			['method' => __METHOD__, 'sql' => $sql, 'index' => $index, 'assoc' => $assoc]
		);

		try{
			return $this->raw_query($sql, $index, $assoc !== null ? $assoc : true);
		}
		catch(Throwable $e){
			throw new DriverException('sql error: ['.get_called_class().'::raw()] '.$e->getMessage());
		}

	}

	/**
	 * @inheritdoc
	 */
	public function prepared(string $sql, array $values = null, string $index = null, bool $assoc = null){
		$this->checkSQL($sql);

		$this->logger->debug(
			'DriverAbstract::prepared()',
			['method' => __METHOD__, 'sql' => $sql, 'val' => $values, 'index' => $index, 'assoc' => $assoc]
		);

		try{
			return $this->prepared_query(
				$sql,
				$values !== null ? $values : [],
				$index,
				$assoc  !== null ? $assoc  : true
			);
		}
		catch(Throwable $e){
			throw new DriverException('sql error: ['.get_called_class().'::prepared()] '.$e->getMessage());
		}

	}

	/**
	 * @inheritdoc
	 * @todo: return array of results
	 */
	public function multi(string $sql, array $values):bool{
		$this->checkSQL($sql);

		if(!is_array($values) || count($values) < 1 || !is_array($values[0]) || count($values[0]) < 1){
			throw new DriverException('invalid data');
		}

		try{
			return $this->multi_query($sql, $values);
		}
		catch(Throwable $e){
			throw new DriverException('sql error: ['.get_called_class().'::multi()] '.$e->getMessage());
		}

	}

	/**
	 * @inheritdoc
	 * @todo: return array of results
	 * @see determine callable type? http://php.net/manual/en/language.types.callable.php#118032
	 */
	public function multiCallback(string $sql, array $data, $callback):bool{
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
		catch(Throwable $e){
			throw new DriverException('sql error: ['.get_called_class().'::multiCallback()] '.$e->getMessage());
		}

	}

	/**
	 * @inheritdoc
	 */
	public function rawCached(string $sql, string $index = null, bool $assoc = null, int $ttl = null){
		$result = $this->cacheGet($sql, [], $index);

		if(!$result){
			$result = $this->raw($sql, $index, $assoc !== null ? $assoc : true);

			$this->cacheSet($sql, $result, [], $index, $ttl);
		}

		return $result;
	}

	/**
	 * @inheritdoc
	 */
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
	 *
	 */
	protected function cacheKey(string $sql, array $values = null, string $index = null):string{
		return hash($this->cachekey_hash_algo, serialize([$sql, $values, $index]));
	}

	/**
	 *
	 */
	protected function cacheGet(string $sql, array $values = null, string $index = null){

		if($this->cache instanceof CacheInterface){
			return $this->cache->get($this->cacheKey($sql, $values, $index));
		}

		return false; // @codeCoverageIgnore
	}

	/**
	 *
	 */
	protected function cacheSet(string $sql, $result, array $values = null, string $index = null, int $ttl = null):bool{

		if($this->cache instanceof CacheInterface){
			return $this->cache->set($this->cacheKey($sql, $values, $index), $result, $ttl);
		}

		return false; // @codeCoverageIgnore
	}

	/**
	 * @throws \chillerlan\Database\Drivers\DriverException
	 */
	protected function checkSQL(string $sql):void{

		if(empty(trim($sql))){
			throw new DriverException('sql error: empty sql');
		}

	}

}
