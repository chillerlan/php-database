<?php
/**
 * Class Drop
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

class Drop extends StatementAbstract{

	public function database(string $dbname):DropDatabase{
		return (new DropDatabase($this->db, $this->dialect, $this->logger))->name($dbname);
	}

	public function table(string $tablename):DropTable{
		return (new DropTable($this->db, $this->dialect, $this->logger))->name($tablename);
	}

}
