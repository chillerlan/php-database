<?php
/**
 * Class MySQLPDO
 *
 * @filesource   MySQLPDO.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 *
 * @noinspection PhpComposerExtensionStubsInspection
 */

namespace chillerlan\Database\Drivers;

use chillerlan\Database\Dialects\MySQL;

/**
 * @property \PDO $db
 */
class MySQLPDO extends PDODriverAbstract{

	protected string $drivername = 'mysql';
	protected string $dialect    = MySQL::class;

	/** @inheritdoc */
	protected function getDSN():string {
		// the charset option is specific to mysql
		return parent::getDSN().';charset='.$this->options->mysql_charset;
	}

}
