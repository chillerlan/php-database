<?php
/**
 * Trait IfNotExistsTrait
 *
 * @filesource   IfNotExistsTrait.php
 * @created      07.01.2018
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

trait IfNotExistsTrait{

	/**
	 * @var bool
	 */
	protected $ifNotExists = false;

	/**
	 * @inheritdoc
	 */
	public function _ifNotExists(){
		$this->ifNotExists = true;

		return $this;
	}

}
