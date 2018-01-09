<?php
/**
 * Class MySQLPDO
 *
 * @filesource   MySQLPDO.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers\PDO
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Query\MySQLQuery;

/**
 * @property \PDO $db
 */
class MySQLPDO extends PDODriverAbstract{

	protected $drivername   = 'mysql';
	protected $querybuilder = MySQLQuery::class;

	/** @inheritdoc */
	protected function getDSN():string {
		// the charset option is specific to mysql
		return parent::getDSN().';charset='.$this->options->mysql_charset;
	}

}
