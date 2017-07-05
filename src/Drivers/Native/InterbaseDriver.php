<?php
/**
 * Class InterbaseDriver
 *
 * @filesource   InterbaseDriver.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers\Native
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers\Native;

use chillerlan\Database\Drivers\{DriverAbstract, DriverException, DriverInterface};

/**
 * likely crashes firebird
 */
class InterbaseDriver extends DriverAbstract{

	protected $ib_service;

	/**
	 * @inheritdoc
	 */
	protected function raw_query(string $sql, string $index = null, bool $assoc = true){
		$result = ibase_query($this->db, $sql);

		if(is_int($result)){
			return true;
		}
		if(is_bool($result)){
			return $result;
		}

		$r = $this->getResult($assoc ? 'ibase_fetch_assoc' : 'ibase_fetch_row', [$result], $index, $assoc);

		ibase_free_result($result);

		return $r;
	}

	/**
	 * @inheritdoc
	 */
	protected function prepared_query(string $sql, array $values = [], string $index = null, bool $assoc = true){
		$stmt = ibase_prepare($sql);

		$result = ibase_execute($stmt, ...array_values($values));

		if(is_int($result)){
			return true;
		}
		if(is_bool($result)){
			return $result;
		}

		$r = $this->getResult($assoc ? 'ibase_fetch_assoc' : 'ibase_fetch_row', [$result], $index, $assoc);

		ibase_free_result($result);
		ibase_free_query($stmt);


		return $r;
	}

	/**
	 * @inheritdoc
	 */
	protected function multi_query(string $sql, array $values){
		// TODO: Implement multi_query() method.
	}

	/**
	 * @inheritdoc
	 */
	protected function multi_callback_query(string $sql, array $data, $callback){
		// TODO: Implement multi_callback_query() method.
	}

	/**
	 * @inheritdoc
	 */
	public function connect(): DriverInterface{

		if(gettype($this->db) === 'resource'){
			return $this;
		}

		try{
			$this->db = ibase_connect($this->options->database, $this->options->username, $this->options->password); // , $charset

			$this->ib_service = ibase_service_attach($this->options->host ?? 'localhost', $this->options->username, $this->options->password);

			return $this;
		}
		catch(\Exception $e){
			throw new DriverException('db error: [InterbaseDriver]: '.$e->getMessage());
		}

	}

	/**
	 * @inheritdoc
	 */
	public function disconnect(): bool{
		ibase_service_detach($this->ib_service);

		return ibase_close($this->db);
	}

	/**
	 * @inheritdoc
	 */
	public function getClientInfo(): string{
		return $this->info();
	}

	/**
	 * @inheritdoc
	 */
	public function getServerInfo(): string{
		return $this->info();
	}

	/**
	 * @inheritdoc
	 */
	public function escape($data){
		return $data;
	}

	/**
	 * @return string
	 */
	protected function info():string{
		return ibase_server_info($this->ib_service, IBASE_SVC_SERVER_VERSION)
			.' ('.ibase_server_info($this->ib_service, IBASE_SVC_IMPLEMENTATION).'), connected to: '.$this->options->database;
	}
}
