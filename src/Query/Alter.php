<?php
/**
 * Class Alter
 *
 * @created      15.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

class Alter extends Statement{

	public function table(string $tablename):AlterTable{
		return (new AlterTable($this->db, $this->dialect, $this->logger))->name($tablename);
	}

	public function database(string $dbname):AlterDatabase{
		return (new AlterDatabase($this->db, $this->dialect, $this->logger))->name($dbname);
	}

}
