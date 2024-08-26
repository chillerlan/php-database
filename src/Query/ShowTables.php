<?php
/**
 * Class ShowTables
 *
 * @created      01.11.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

use function trim;

class ShowTables extends Statement implements Where, Query{

	protected string|null $pattern = null;

	public function from(string $name):static{
		return $this->setName($name);
	}

	public function where(mixed $val1, mixed $val2 = null, string|null $operator = null, bool|null $bind = null, string|null $join = null):static{
		return $this->setWhere($val1, $val2, $operator, $bind, $join);
	}

	public function openBracket(string|null $join = null):static{
		return $this->setOpenBracket($join);
	}

	public function closeBracket():static{
		return $this->setCloseBracket();
	}

	/** @inheritdoc */
	protected function getSQL():array{
		return $this->dialect->showTables($this->name, $this->pattern, $this->_getWhere());
	}

	public function pattern(string $pattern):static{
		$pattern = trim($pattern);

		if(!empty($pattern)){
			$this->pattern = $pattern;
		}

		return $this;
	}

}
