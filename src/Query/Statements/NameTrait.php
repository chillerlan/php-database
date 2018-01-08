<?php
/**
 * Trait NameTrait
 *
 * @filesource   NameTrait.php
 * @created      07.01.2018
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

trait NameTrait{

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @inheritdoc
	 */
	public function _name(string $name){
		$this->name = trim($name);

		if(empty($this->name)){
			throw new StatementException('no name specified');
		}

		return $this;
	}

}
