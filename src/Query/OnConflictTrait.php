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

	protected string $name;
	protected ?string $on_conflict = null;
	protected ?string $conflict_target = null;

	/**
	 * @param string      $name
	 * @param string|null $on_conflict
	 * @param string|null $conflict_target
	 *
	 * @return $this
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function name(string $name, string $on_conflict = null, string $conflict_target = null):Statement{
		$this->name      = trim($name);
		$on_conflict     = trim(strtoupper($on_conflict));
		$conflict_target = trim(strtoupper($conflict_target));

		if(empty($this->name)){
			throw new QueryException('no name specified');
		}

		if(!empty($on_conflict)){
			$this->on_conflict = $on_conflict;
		}

		if(!empty($conflict_target)){
			$this->conflict_target = $conflict_target;
		}

		return $this;
	}

}
