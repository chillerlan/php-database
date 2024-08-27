<?php
/**
 * Class Show
 *
 * @created      09.01.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/show.html
 */
class Show extends StatementAbstract{

	public function createTable(string $tablename):ShowCreateTable{
		return (new ShowCreateTable($this->db, $this->dialect, $this->logger))->name($tablename);
	}

	public function databases():ShowDatabases{
		return new ShowDatabases($this->db, $this->dialect, $this->logger);
	}

	public function tables(string|null $from = null):ShowTables{
		$showTables = new ShowTables($this->db, $this->dialect, $this->logger);

		if(!empty($from)){
			$showTables->from($from);
		}

		return $showTables;
	}

#	public function columns():ShowColumns;
#	public function index():ShowIndex;
#	public function collation():ShowCollation;
#	public function characterSet():ShowCharacterSet;
#	public function tableStatus():ShowTableStatus;

}
