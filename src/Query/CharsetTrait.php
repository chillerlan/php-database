<?php
/**
 * Trait CharsetTrait
 *
 * @filesource   CharsetTrait.php
 * @created      08.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

trait CharsetTrait{

	protected ?string $charset = null;

	/**
	 * @param string $charset
	 *
	 * @return $this
	 */
	public function charset(string $charset):Statement{
		$this->charset = trim($charset);

		return $this;
	}

}
