<?php
/**
 * Class Create
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

class Create extends StatementAbstract{

#	public function index():CreateIndex;
#	public function view():CreateView;
#	public function trigger():CreateTrigger;

	public function database(string $dbname):CreateDatabase{
		return (new CreateDatabase($this->db, $this->dialect, $this->logger))->name($dbname);
	}

	public function table(string $tablename):CreateTable{
		return (new CreateTable($this->db, $this->dialect, $this->logger))->name($tablename);
	}

}
