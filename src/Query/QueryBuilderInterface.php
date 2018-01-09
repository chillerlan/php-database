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
use chillerlan\Database\Query\{
	Alter\Alter, Create\Create, Delete\Delete, Drop\Drop, Insert\Insert, Select\Select, Show\Show, Truncate\Truncate, Update\Update
};
use Psr\Log\LoggerInterface;

/**
 * @property \chillerlan\Database\Query\Alter\Alter       $alter
 * @property \chillerlan\Database\Query\Create\Create     $create
 * @property \chillerlan\Database\Query\Delete\Delete     $delete
 * @property \chillerlan\Database\Query\Drop\Drop         $drop
 * @property \chillerlan\Database\Query\Insert\Insert     $insert
 * @property \chillerlan\Database\Query\Select\Select     $select
 * @property \chillerlan\Database\Query\Show\Show         $show
 * @property \chillerlan\Database\Query\Truncate\Truncate $truncate
 * @property \chillerlan\Database\Query\Update\Update     $update
 *
 * @method setLogger(\Psr\Log\LoggerInterface $logger):QueryBuilderInterface
 */
interface QueryBuilderInterface{

	public const STATEMENTS = ['alter', 'create', 'delete', 'drop', 'insert', 'select', 'show', 'truncate', 'update'];

	/**
	 * QueryBuilderAbstract constructor.
	 *
	 * @param \chillerlan\Database\Drivers\DriverInterface $db
	 * @param \chillerlan\Database\DatabaseOptions         $options
	 * @param \Psr\Log\LoggerInterface|null                $logger
	 */
	public function __construct(DriverInterface $db, DatabaseOptions $options, LoggerInterface $logger = null);

	/**
	 * @param $name
	 *
	 * @return mixed
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function __get(string $name);

	/**
	 * @return \chillerlan\Database\Query\Alter\Alter
	 */
	public function alter():Alter;

	/**
	 * @return \chillerlan\Database\Query\Create\Create
	 */
	public function create():Create;

	/**
	 * @return \chillerlan\Database\Query\Delete\Delete
	 */
	public function delete():Delete;

	/**
	 * @return \chillerlan\Database\Query\Drop\Drop
	 */
	public function drop():Drop;

	/**
	 * @return \chillerlan\Database\Query\Insert\Insert
	 */
	public function insert():Insert;

	/**
	 * @return \chillerlan\Database\Query\Select\Select
	 */
	public function select():Select;

	/**
	 * @return \chillerlan\Database\Query\Show\Show
	 */
	public function show():Show;

	/**
	 * @return \chillerlan\Database\Query\Truncate\Truncate
	 */
	public function truncate():Truncate;

	/**
	 * @return \chillerlan\Database\Query\Update\Update
	 */
	public function update():Update;

}
