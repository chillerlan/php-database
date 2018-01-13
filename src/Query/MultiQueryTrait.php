<?php
/**
 * Trait MultiQueryTrait
 *
 * @filesource   MultiQueryTrait.php
 * @created      13.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use Closure;

/**
 * @implements \chillerlan\Database\MultiQuery
 */
trait MultiQueryTrait{
	use QueryTrait;

	/** @inheritdoc */
	public function multi(iterable $values = null){

		if($this instanceof Insert || $this instanceof Update){
			$bindvalues = $this instanceof BindValues ? $this->getBindValues() : [];

			return $this->db->multi($this->sql(true), $values ?? $bindvalues);
		}

		throw new QueryException('INSERT or UPDATE only');
	}

	/** @inheritdoc */
	public function callback(iterable $values, Closure $callback){

		if($this instanceof Insert || $this instanceof Update){
			return $this->db->multiCallback($this->sql(true), $values, $callback);
		}

		throw new QueryException('INSERT or UPDATE only');
	}

}
