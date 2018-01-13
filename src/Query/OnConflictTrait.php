<?php
/**
 * Trait OnConflictTrait
 *
 * @filesource   OnConflictTrait.php
 * @created      09.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

trait OnConflictTrait{

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $on_conflict;

	/**
	 * @param string      $name
	 * @param string|null $on_conflict
	 *
	 * @return $this
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function name(string $name, string $on_conflict = null){
		$this->name  = trim($name);
		$on_conflict = trim(strtoupper($on_conflict));

		if(empty($this->name)){
			throw new QueryException('no name specified');
		}

		if(!empty($on_conflict)){
			$this->on_conflict = $on_conflict;
		}

		return $this;
	}

}
