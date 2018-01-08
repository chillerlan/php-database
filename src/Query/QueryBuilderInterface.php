<?php
/**
 * Interface QueryBuilderInterface
 *
 * @filesource   QueryBuilderInterface.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use chillerlan\Database\{
	DatabaseOptions, Drivers\DriverInterface
};
use chillerlan\Database\Query\Statements\{
	Alter, Create, Delete, Drop, Insert, Select, Update
};
use Psr\Log\LoggerAwareInterface;

/**
 * @property \chillerlan\Database\Query\Statements\Select $select
 * @property \chillerlan\Database\Query\Statements\Insert $insert
 * @property \chillerlan\Database\Query\Statements\Update $update
 * @property \chillerlan\Database\Query\Statements\Delete $delete
 * @property \chillerlan\Database\Query\Statements\Create $create
 * @property \chillerlan\Database\Query\Statements\Drop   $drop
 * @property \chillerlan\Database\Query\Statements\Alter  $alter
 */
interface QueryBuilderInterface extends LoggerAwareInterface{

	public const STATEMENTS = ['select', 'insert', 'update', 'delete', 'create', 'alter', 'drop'];

	/**
	 * QueryBuilderAbstract constructor.
	 *
	 * @param \chillerlan\Database\Drivers\DriverInterface $db
	 * @param \chillerlan\Database\DatabaseOptions         $options
	 */
	public function __construct(DriverInterface $db, DatabaseOptions $options);

	/**
	 * @return \chillerlan\Database\Query\Statements\Select
	 */
	public function select():Select;

	/**
	 * @return \chillerlan\Database\Query\Statements\Insert
	 */
	public function insert():Insert;

	/**
	 * @return \chillerlan\Database\Query\Statements\Update
	 */
	public function update():Update;

	/**
	 * @return \chillerlan\Database\Query\Statements\Delete
	 */
	public function delete():Delete;

	/**
	 * @return \chillerlan\Database\Query\Statements\Create
	 */
	public function create():Create;

	/**
	 * @return \chillerlan\Database\Query\Statements\Alter
	 */
	public function alter():Alter;

	/**
	 * @return \chillerlan\Database\Query\Statements\Drop
	 */
	public function drop():Drop;

}
