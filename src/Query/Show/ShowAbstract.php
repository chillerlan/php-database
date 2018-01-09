<?php
/**
 * Class ShowAbstract
 *
 * @filesource   ShowAbstract.php
 * @created      09.01.2018
 * @package      chillerlan\Database\Query\Show
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Show;

use chillerlan\Database\Query\StatementAbstract;

abstract class ShowAbstract extends StatementAbstract implements Show{

	public function sql():string{

	}

	public function createTable(string $tablename):Show{

		 return $this;
	 }

	 public function createDatabase(string $dbname):Show{

		 return $this;
	 }

	 public function createFunction(string $func):Show{

		 return $this;
	 }

	 public function createProcedure(string $proc):Show{

		return $this;
	 }

	 public function databases():ShowDatabases{
		 return new class($this->db, $this->options, $this->quotes) extends ShowCommon implements ShowDatabases{};
	 }

	 public function tables():ShowTables{
		 return new class($this->db, $this->options, $this->quotes) extends ShowCommon implements ShowTables{};
	 }

	 public function columns():ShowColumns{
		 return new class($this->db, $this->options, $this->quotes) extends ShowCommon implements ShowColumns{};
	 }

	 public function index():ShowIndex{
		 return new class($this->db, $this->options, $this->quotes) extends ShowCommon implements ShowIndex{};
	 }

	 public function collation():ShowCollation{
		 return new class($this->db, $this->options, $this->quotes) extends ShowCommon implements ShowCollation{};
	 }

	 public function characterSet():ShowCharacterSet{
		 return new class($this->db, $this->options, $this->quotes) extends ShowCommon implements ShowCharacterSet{};
	 }

	 public function tableStatus():ShowTableStatus{
		 return new class($this->db, $this->options, $this->quotes) extends ShowCommon implements ShowTableStatus{};
	 }
 }
