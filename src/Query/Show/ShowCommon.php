<?php
/**
 * Class ShowCommon
 *
 * @filesource   ShowCommon.php
 * @created      09.01.2018
 * @package      chillerlan\Database\Query\Show
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Show;

use chillerlan\Database\Query\StatementAbstract;
use chillerlan\Database\Query\WhereTrait;

class ShowCommon extends StatementAbstract{
	use WhereTrait;

	public function sql():string{

	}


}
