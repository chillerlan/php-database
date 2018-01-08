<?php
/**
 * Trait CharsetTrait
 *
 * @filesource   CharsetTrait.php
 * @created      08.01.2018
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

trait CharsetTrait{

	/**
	 * @var string
	 */
	protected $charset;

	/**
	 * @inheritdoc
	 */
	public function _charset(string $charset){
		$this->charset = trim($charset);

		return $this;
	}

}
