<?php
/**
 * Interface ShowCreate
 *
 * @filesource   ShowCreate.php
 * @created      15.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

interface ShowCreate extends Statement{

	public function table(string $tablename):ShowItem;
#	public function database(string $dbname);
#	public function function(string $func);
#	public function procedure(string $proc);

}
