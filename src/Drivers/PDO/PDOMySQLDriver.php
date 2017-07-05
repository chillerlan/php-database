<?php
/**
 * Class PDOMySQLDriver
 *
 * @filesource   PDOMySQLDriver.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Drivers\PDO
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Drivers\PDO;

class PDOMySQLDriver extends PDODriverAbstract{

	/**
	 * @inheritdoc
	 */
	protected $drivername = 'mysql';

	/**
	 * @inheritdoc
	 */
	protected function getDSN():string {
		// the charset option is specific to mysql
		return parent::getDSN().';charset='.$this->options->mysql_charset;
	}

}
