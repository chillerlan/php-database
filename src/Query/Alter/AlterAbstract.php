<?php
/**
 * Class AlterAbstract
 *
 * @filesource   AlterAbstract.php
 * @created      07.01.2018
 * @package      chillerlan\Database\Query\Alter
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Alter;

use chillerlan\Database\Query\StatementAbstract;

abstract class AlterAbstract extends StatementAbstract implements Alter{

	 public function table():AlterTable{
		 return (new class($this->db, $this->options, $this->quotes) extends AlterTableAbstract{})->name($tablename);
	 }

	 public function database():AlterDatabase{
		 return (new class($this->db, $this->options, $this->quotes) extends AlterDatabaseAbstract{})->name($dbname);
	 }
 }
