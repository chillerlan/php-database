<?php
/**
 * Class PostgreSQLPDO
 *
 * @filesource   PostgreSQLPDO.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers\PDO
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Query\PostgresQuery;

/**
 * @property \PDO $db
 */
class PostgreSQLPDO extends PDODriverAbstract{

	protected $drivername   = 'pgsql';
	protected $querybuilder = PostgresQuery::class;

}
