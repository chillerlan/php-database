<?php
/**
 * Trait IfNotExistsTrait
 *
 * @filesource   IfNotExistsTrait.php
 * @created      07.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

trait IfNotExistsTrait{

	/**
	 * @var bool
	 */
	protected $ifNotExists = false;

	/**
	 * @return $this
	 */
	public function ifNotExists(){
		$this->ifNotExists = true;

		return $this;
	}

}
