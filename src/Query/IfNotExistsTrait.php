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

	protected bool $ifNotExists = false;

	/**
	 * @return $this
	 */
	public function ifNotExists():Statement{
		$this->ifNotExists = true;

		return $this;
	}

}
