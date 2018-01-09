<?php
/**
 * Class DropAbstract
 *
 * @filesource   DropAbstract.php
 * @created      07.01.2018
 * @package      chillerlan\Database\Query\Drop
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Drop;

use chillerlan\Database\Query\{StatementAbstract, StatementException};

abstract class DropAbstract extends StatementAbstract implements Drop{

	/** @inheritdoc */
	public function table(string $tablename):DropTable{

		if(empty($tablename)){
			throw new StatementException('no name specified');
		}

		return (new class($this->db, $this->options, $this->quotes) extends DropTableAbstract{})->name($tablename);
	}

	/** @inheritdoc */
	public function database(string $dbname):DropDatabase{

		if(empty($dbname)){
			throw new StatementException('no name specified');
		}

		return (new class($this->db, $this->options, $this->quotes) extends DropDatabaseAbstract{})->name($dbname);
	}

}