<?php
/**
 * Class ShowCreateTable
 *
 * @created      01.11.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

class ShowCreateTable extends StatementAbstract implements Query{

	public function name(string $name):static{
		return $this->setName($name);
	}

	protected function sql():array{
		return $this->dialect->showCreateTable($this->name);
	}

}
