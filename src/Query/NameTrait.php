<?php
/**
 * Trait NameTrait
 *
 * @filesource   NameTrait.php
 * @created      07.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

trait NameTrait{

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @param string $name
	 *
	 * @return $this
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function name(string $name){
		$this->name = trim($name);

		if(empty($this->name)){
			throw new QueryException('no name specified');
		}

		return $this;
	}

}
