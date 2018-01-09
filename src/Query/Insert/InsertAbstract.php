<?php
/**
 * Class InsertAbstract
 *
 * @filesource   InsertAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Insert
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Insert;

use chillerlan\Database\Query\{
	NameTrait, StatementAbstract, StatementException
};

abstract class InsertAbstract extends StatementAbstract implements Insert{
	use NameTrait{
		name as into;
	}

	/** @inheritdoc */
	public function sql():string{

		if(empty($this->bindValues)){
			throw new StatementException('no values given');
		}

		$fields = $this->multi ? array_keys($this->bindValues[0]) : array_keys($this->bindValues);

		$sql  = 'INSERT ';
		$sql .= 'INTO '.$this->quote($this->name);
		$sql .= ' ('.$this->quotes[0].implode($this->quotes[1].', '.$this->quotes[0], $fields).$this->quotes[1].')';
		$sql .= ' VALUES ('.implode(',', array_fill(0, count($fields), '?')).')';

		return $sql;
	}

	/**
	 * @todo
	 *
	 * @inheritdoc
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
