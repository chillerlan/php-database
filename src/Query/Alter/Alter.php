<?php
/**
 * Interface Alter
 *
 * @filesource   Alter.php
 * @created      15.06.2017
 * @package      chillerlan\Database\Query\Alter
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Alter;

use chillerlan\Database\Query\Statement;

interface Alter extends Statement{

	public function table():AlterTable;
	public function database():AlterDatabase;

}
