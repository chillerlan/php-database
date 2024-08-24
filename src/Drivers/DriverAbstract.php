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
use Closure;
use Throwable;
use Psr\Log\{LoggerInterface, NullLogger};
use Psr\SimpleCache\CacheInterface;

use function bin2hex, count, floatval, hash, intval, is_array, is_bool,
	is_callable, is_float, is_int, is_numeric, serialize, trim;

/**
 *
 */
abstract class DriverAbstract implements DriverInterface{

	/** @var \chillerlan\Database\DatabaseOptions */
	protected SettingsContainerInterface $options;
	protected LoggerInterface $logger;
	protected CacheInterface|null $cache = null;
	protected string $cachekey_hash_algo;
	protected string|null $convert_encoding_src;
	protected string|null $convert_encoding_dest;

	/**
	 * Constructor.
	 *
	 * @param \chillerlan\Settings\SettingsContainerInterface $options
	 * @param \Psr\SimpleCache\CacheInterface|null $cache
	 * @param \Psr\Log\LoggerInterface|null        $logger
	 */
	public function __construct(SettingsContainerInterface $options, CacheInterface|null $cache = null, LoggerInterface|null $logger = null){
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
	 *
	 */
	abstract protected function raw_query(string $sql, string|null $index = null, bool|null $assoc = null):Result;

	/**
	 *
	 */
	abstract protected function prepared_query(string $sql, array|null $values = null, string|null $index = null, bool|null $assoc = null):Result;

	/**
	 *
	 */
	abstract protected function multi_query(string $sql, array $values):bool;

	/**
	 *
	 */
	abstract protected function multi_callback_query(string $sql, array $data, Closure $callback):bool;

	/**
	 * Sets a logger.
	 */
	public function setLogger(LoggerInterface $logger):void{
		$this->logger = $logger;
	}

	/**
	 * @inheritdoc
	 */
	public function escape(mixed $data = null):mixed{

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
	public function raw(string $sql, string|null $index = null, bool|null $assoc = null):Result{
		$this->checkSQL($sql);

		$this->logger->debug(
			'DriverAbstract::raw()',
			['method' => __METHOD__, 'sql' => $sql, 'index' => $index, 'assoc' => $assoc]
		);

		try{
			return $this->raw_query($sql, $index, $assoc !== null ? $assoc : true);
		}
		catch(Throwable $e){
			throw new DriverException('sql error: ['.static::class.'::raw()] '.$e->getMessage());
		}

	}

	/**
	 * @inheritdoc
	 */
	public function prepared(string $sql, array|null $values = null, string|null $index = null, bool|null $assoc = null):Result{
		$this->checkSQL($sql);

		$this->logger->debug(
			'DriverAbstract::prepared()',
			['method' => __METHOD__, 'sql' => $sql, 'val' => $values, 'index' => $index, 'assoc' => $assoc]
		);

		try{
			return $this->prepared_query($sql, ($values ?? []), $index, ($assoc ?? true));
		}
		catch(Throwable $e){
			throw new DriverException('sql error: ['.static::class.'::prepared()] '.$e->getMessage());
		}

	}

	/**
	 * @inheritdoc
	 * @todo: return array of results
	 */
	public function multi(string $sql, array $values):bool{
		$this->checkSQL($sql);

		if(count($values) < 1 || !is_array($values[0]) || count($values[0]) < 1){
			throw new DriverException('invalid data');
		}

		try{
			return $this->multi_query($sql, $values);
		}
		catch(Throwable $e){
			throw new DriverException('sql error: ['.static::class.'::multi()] '.$e->getMessage());
		}

	}

	/**
	 * @inheritdoc
	 * @todo: return array of results
	 * @see determine callable type? http://php.net/manual/en/language.types.callable.php#118032
	 */
	public function multiCallback(string $sql, array $data, Closure $callback):bool{
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
			throw new DriverException('sql error: ['.static::class.'::multiCallback()] '.$e->getMessage());
		}

	}

	/**
	 * @inheritdoc
	 */
	public function rawCached(string $sql, string|null $index = null, bool|null $assoc = null, int|null $ttl = null):Result{
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
	public function preparedCached(string $sql, array|null $values = null, string|null $index = null, bool|null $assoc = null, int|null $ttl = null):Result{
		$result = $this->cacheGet($sql, $values, $index);

		if(!$result){
			$result = $this->prepared($sql, $values, $index, $assoc);

			$this->cacheSet($sql, $result, $values, $index, $ttl);
		}

		return $result;
	}

	/**
	 * @inheritdoc
	 */
	protected function getResult(Closure $callable, array $args, string|null $index = null, bool|null $assoc = null):Result{
		$out = new Result(null, $this->convert_encoding_src, $this->convert_encoding_dest);
		$i   = 0;

		/** @noinspection PhpAssignmentInConditionInspection */
		while($row = $callable(...$args)){
			$key = $assoc && !empty($index) ? $row[$index] : $i;

			$out[$key] = $row;
			$i++;
		}

		return $out;
	}

	/**
	 *
	 */
	protected function cacheKey(string $sql, array|null $values = null, string|null $index = null):string{
		return hash($this->cachekey_hash_algo, serialize([$sql, $values, $index]));
	}

	/**
	 *
	 */
	protected function cacheGet(string $sql, array|null $values = null, string|null $index = null):mixed{

		if($this->cache instanceof CacheInterface){
			return $this->cache->get($this->cacheKey($sql, $values, $index));
		}

		return false; // @codeCoverageIgnore
	}

	/**
	 *
	 */
	protected function cacheSet(string $sql, $result, array|null $values = null, string|null $index = null, int|null $ttl = null):bool{

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
