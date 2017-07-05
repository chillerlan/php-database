<?php
/**
 * Interface Statement
 *
 * @filesource   Statement.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

interface Statement{

	/**
	 * @return string
	 */
	public function sql():string;

	/**
	 * @return array
	 */
	public function bindValues():array;

	/**
	 * @param string|null   $index
	 * @param array|null    $values
	 * @param callable|null $callback
	 *
	 * @return bool|\chillerlan\Database\Result
	 */
	public function execute(string $index = null, array $values = null, $callback = null);

}
