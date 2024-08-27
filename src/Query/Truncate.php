<?php
/**
 * Class Truncate
 *
 * @created      09.01.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

/**
 * @link https://dev.mysql.com/doc/refman/5.7/en/truncate-table.html
 * @link https://www.postgresql.org/docs/current/static/sql-truncate.html
 * @link https://docs.microsoft.com/en-us/sql/t-sql/statements/truncate-table-transact-sql
 */
class Truncate extends StatementAbstract implements Query{

	public function table(string $name):static{
		return $this->setName($name);
	}

	protected function sql():array{
		return $this->dialect->truncate($this->name);
	}

}
