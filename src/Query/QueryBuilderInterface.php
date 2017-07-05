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

use chillerlan\Database\Query\Statements\{
	Alter, Create, Delete, Drop,
	Insert, Select, Update
};

/**
 * @property \chillerlan\Database\Query\Statements\Select $select
 * @property \chillerlan\Database\Query\Statements\Insert $insert
 * @property \chillerlan\Database\Query\Statements\Update $update
 * @property \chillerlan\Database\Query\Statements\Delete $delete
 * @property \chillerlan\Database\Query\Statements\Create $create
 * @property \chillerlan\Database\Query\Statements\Drop   $drop
 * @property \chillerlan\Database\Query\Statements\Alter  $alter
 */
interface QueryBuilderInterface{

	public function select():Select;
	public function insert():Insert;
	public function update():Update;
	public function delete():Delete;
	public function create():Create;
	public function alter():Alter;
	public function drop():Drop;

}
