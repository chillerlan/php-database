<?php
/**
 * Class ShowTables
 *
 * @created      01.11.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use function trim;

class ShowTables extends Statement implements Where, Query{

	protected ?string $pattern = null;

	public function from(string $name):ShowTables{
		return $this->setName($name);
	}

	public function where($val1, $val2 = null, string $operator = null, bool $bind = null, string $join = null):ShowTables{
		return $this->setWhere($val1, $val2, $operator, $bind, $join);
	}

	public function openBracket(string $join = null):ShowTables{
		return $this->setOpenBracket($join);
	}

	public function closeBracket():ShowTables{
		return $this->setCloseBracket();
	}

	/** @inheritdoc */
	protected function getSQL():array{
		return $this->dialect->showTables($this->name, $this->pattern, $this->_getWhere());
	}

	public function pattern(string $pattern):ShowTables{
		$pattern = trim($pattern);

		if(!empty($pattern)){
			$this->pattern = $pattern;
		}

		return $this;
	}

}
