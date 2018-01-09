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

use chillerlan\Database\Query\Insert\Insert;

/**
 * @extends \chillerlan\Database\Query\Insert\Insert
 */
trait OnConflictTrait{

	/**
	 * @var string
	 */
	protected $on_conflict;

	/**
	 * @param string|null $on_conflict
	 *
	 * @return $this
	 */
	private function _on_conflict(string $on_conflict = null){
		$on_conflict = trim(strtoupper($on_conflict));

		if(!empty($on_conflict)){
			$this->on_conflict = $on_conflict;
		}

		return $this;
	}

	/**
	 * @param string      $table
	 * @param string|null $on_conflict
	 *
	 * @return $this
	 */
	public function into(string $table, string $on_conflict = null):Insert{
		$this->_on_conflict($on_conflict);

		/** @noinspection PhpUndefinedClassInspection */
		return parent::into($table);
	}

}
