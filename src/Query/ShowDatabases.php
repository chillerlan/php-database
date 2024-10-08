<?php
/**
 * Class ShowDatabases
 *
 * @created      01.11.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

class ShowDatabases extends StatementAbstract implements Query{

	protected function sql():array{
		return $this->dialect->showDatabases(); // @todo? WHERE
	}

}
