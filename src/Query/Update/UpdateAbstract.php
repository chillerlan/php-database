<?php
/**
 * Class UpdateAbstract
 *
 * @filesource   UpdateAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Update
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Update;

use chillerlan\Database\Query\{
	NameTrait, Statement, StatementAbstract, StatementException, Where, WhereTrait
};

/** @noinspection PhpSuperClassIncompatibleWithInterfaceInspection */
abstract class UpdateAbstract extends StatementAbstract implements Update, Where{
	use WhereTrait, NameTrait{
		name as table;
	}

	/**
	 * @var array
	 */
	protected $set = [];

	/** @inheritdoc */
	public function sql():string{

		if(empty($this->set)){
			throw new StatementException('no fields to update specified');
		}

		$sql  = 'UPDATE ';
		$sql .= $this->quote($this->name);
		$sql .= ' SET ';
		$sql .= implode(', ', $this->set);
		$sql .= $this->_getWhere();

		return $sql;
	}

	/** @inheritdoc */
	public function set(array $set, bool $bind = null):Update{
		$bind = $bind !== null ? $bind : true;

		foreach($set as $k => $v){

			if($v instanceof Statement){
				$this->set[] = $this->quote($k).' = ('.$v->sql().')';
				$this->bindValues = array_merge($this->bindValues, $v->bindValues());
			}
			elseif(is_array($v)){
				// @todo: [expr, bindval1, bindval2, ...]
			}
			else{
				if($bind){
					$this->set[] = $this->quote($k).' = ?';
					$this->bindValues[] = $v;
				}
				else{
					$this->set[] = is_int($k)
						? $this->quote($v).' = ?'
						: $this->quote($k).' = '.$v;
				}
			}
		}

		return $this;
	}

}
