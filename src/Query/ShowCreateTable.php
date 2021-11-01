<?php
/**
 * Class ShowCreateTable
 *
 * @created      01.11.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

class ShowCreateTable extends Statement implements Query{

	public function name(string $name):ShowCreateTable{
		return $this->setName($name);
	}

	/** @inheritdoc */
	protected function getSQL():array{
		return $this->dialect->showCreateTable($this->name);
	}

}
