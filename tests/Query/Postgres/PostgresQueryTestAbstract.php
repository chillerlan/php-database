<?php
/**
 * Class PostgresQueryTestAbstract
 *
 * @filesource   PostgresQueryTestAbstract.php
 * @created      29.06.2017
 * @package      chillerlan\DatabaseTest\Query\Postgres
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\DatabaseTest\Query\Postgres;

use chillerlan\Database\Query\Dialects\PostgresQueryBuilder;
use chillerlan\DatabaseTest\Query\QueryTestAbstract;

abstract class PostgresQueryTestAbstract extends QueryTestAbstract{

	protected $querydriver = PostgresQueryBuilder::class;
	protected $envVar = 'DB_POSTGRES_';

}
