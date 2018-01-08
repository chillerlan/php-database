<?php
/**
 * Trait IfExistsTrait
 *
 * @filesource   IfExistsTrait.php
 * @created      08.01.2018
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

/**
 */
trait IfExistsTrait{

	/**
	 * @var bool
	 */
	protected $ifExists = false;

	/**
	 * @inheritdoc
	 */
	public function _ifExists(){
		$this->ifExists = true;

		return $this;
	}

}
