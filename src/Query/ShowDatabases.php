<?php
/**
 * Class ShowDatabases
 *
 * @created      01.11.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

class ShowDatabases extends Statement implements Query{

	/** @inheritdoc */
	protected function getSQL():array{
		return $this->dialect->showDatabases(); // @todo? WHERE
	}

}
