<?php
/**
 * Class QueryBuilderAbstract
 *
 * @filesource   QueryBuilderAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use chillerlan\Database\Drivers\DriverInterface;
use chillerlan\Database\Options;

abstract class QueryBuilderAbstract implements QueryBuilderInterface{

	/**
	 * @var \chillerlan\Database\Drivers\DriverInterface
	 */
	protected $db;

	/**
	 * @var \chillerlan\Database\Options
	 */
	protected $options;

	/**
	 * @var string[]
	 */
	protected $quotes;

	/**
	 * QueryBuilderAbstract constructor.
	 *
	 * @param \chillerlan\Database\Drivers\DriverInterface $db
	 * @param \chillerlan\Database\Options                 $options
	 */
	public function __construct(DriverInterface $db, Options $options){
		$this->db      = $db;
		$this->options = $options;
	}

	/**
	 * @param $name
	 *
	 * @return mixed
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function __get($name){
		$name = strtolower($name);

		if(in_array($name, ['select', 'insert', 'update', 'delete', 'create', 'alter', 'drop'])){
			return $this->{$name}();
		}

		throw new QueryException('invalid statement');
	}


}
