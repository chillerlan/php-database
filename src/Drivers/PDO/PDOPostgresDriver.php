<?php
/**
 * Class PDOPostgresDriver
 *
 * @filesource   PDOPostgresDriver.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers\PDO
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers\PDO;

class PDOPostgresDriver extends PDODriverAbstract{

	/**
	 * @inheritdoc
	 */
	protected $drivername = 'pgsql';

}
