<?php
/**
 * Class InsertAbstract
 *
 * @filesource   InsertAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Dialects;

use chillerlan\Database\Query\QueryException;
use chillerlan\Database\Query\Statements\Insert;

abstract class InsertAbstract extends StatementAbstract implements Insert{

	/**
	 * @var string
	 */
	protected $table;

	/**
	 * @var array
	 */
#	protected $fields = [];

	/**
	 * @return string
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function sql():string{

		if(empty($this->table)){
			throw new QueryException('no table specified');
		}

		if(empty($this->bindValues)){
			throw new QueryException('no values given');
		}

		$fields = $this->multi ? array_keys($this->bindValues[0]) : array_keys($this->bindValues);

		$sql  = 'INSERT ';
		$sql .= 'INTO '.$this->quote($this->table);
		$sql .= ' ('.$this->quotes[0].implode($this->quotes[1].', '.$this->quotes[0], $fields).$this->quotes[1].')';
		$sql .= ' VALUES ('.implode(',', array_fill(0, count($fields), '?')).')';

		return $sql;
	}

	/**
	 * @param string $table
	 *
	 * @return \chillerlan\Database\Query\Statements\Insert
	 */
	public function into(string $table):Insert{
		$this->table = trim($table);

		return $this;
	}

	/**
	 * @param array $values
	 *
	 * @return \chillerlan\Database\Query\Statements\Insert
	 */
	public function values(array $values):Insert{
		$this->multi = count($values) > 0 && (
			isset($values[0]) && is_array($values[0]) && count($values[0]) > 0
#			|| $values[0] instanceof \Traversable
#			|| $values[0] instanceof \stdClass
		);

		$this->bindValues = $values;

		return $this;
	}

}
