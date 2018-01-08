<?php
/**
 * Class CreateAbstract
 *
 * @filesource   CreateAbstract.php
 * @created      07.01.2018
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Statements;

abstract class CreateAbstract extends StatementAbstract implements Create{

	/**
	 * @inheritdoc
	 */
	 public function database(string $dbname):CreateDatabase{

		 /**
		  * @inheritdoc
		  */
		 return (new class($this->db, $this->options, $this->quotes) extends CreateDatabaseAbstract{

		 })->name($dbname);

	 }

	/**
	 * @inheritdoc
	 */
	 public function table(string $tablename):CreateTable{

		 /**
		  * @inheritdoc
		  */
		 return (new class($this->db, $this->options, $this->quotes) extends CreateTableAbstract{

		 })->name($tablename);

	 }

 }
