<?php
/**
 * Trait IfExistsTrait
 *
 * @filesource   IfExistsTrait.php
 * @created      08.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

trait IfExistsTrait{

	/**
	 * @var bool
	 */
	protected $ifExists = false;

	/**
	 * @return $this
	 */
	public function ifExists(){
		$this->ifExists = true;

		return $this;
	}

}
