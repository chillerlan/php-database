<?php
/**
 * Class PostgreSQLPDO
 *
 * @filesource   PostgreSQLPDO.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Dialects\Postgres;

/**
 * @property \PDO $db
 */
class PostgreSQLPDO extends PDODriverAbstract{

	protected $drivername = 'pgsql';
	protected $dialect    = Postgres::class;

}
