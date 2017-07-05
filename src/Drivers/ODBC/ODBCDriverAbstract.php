<?php
/**
 * Class ODBCDriverAbstract
 *
 * @filesource   ODBCDriverAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers\ODBC
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers\ODBC;

use chillerlan\Database\Drivers\DriverAbstract;
use chillerlan\Database\Drivers\DriverInterface;

class ODBCDriverAbstract extends DriverAbstract{

	/**
	 * @inheritdoc
	 */
	protected function raw_query(string $sql, string $index = null, bool $assoc = true){
		// TODO: Implement raw_query() method.
	}

	/**
	 * @inheritdoc
	 */
	protected function prepared_query(string $sql, array $values = [], string $index = null, bool $assoc = true){
		// TODO: Implement prepared_query() method.
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
	public function connect():DriverInterface{
		// TODO: Implement connect() method.
	}

	/**
	 * @inheritdoc
	 */
	public function disconnect():bool{
		// TODO: Implement disconnect() method.
	}

	/**
	 * @inheritdoc
	 */
	public function getClientInfo():string{
		// TODO: Implement getClientInfo() method.
	}

	/**
	 * @inheritdoc
	 */
	public function getServerInfo():string{
		// TODO: Implement getServerInfo() method.
	}

	/**
	 * @inheritdoc
	 */
	public function escape($data){
		// TODO: Implement escape() method.
	}
}
