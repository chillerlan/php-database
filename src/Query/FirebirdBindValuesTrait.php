<?php
/**
 * Trait FirebirdBindValuesTrait
 *
 * @filesource   FirebirdBindValuesTrait.php
 * @created      12.06.2017
 * @package      chillerlan\Database\Query\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

trait FirebirdBindValuesTrait{

	protected $offset;
	protected $limit;
	protected $bindValues = [];

	public function bindValues():array{

		if(!is_null($this->limit)){

			$this->bindValues = array_merge([
				'limit'  => $this->limit,
				'offset' => !is_null($this->offset) ? $this->offset : 0,
			], $this->bindValues);

		}

		return $this->bindValues;
	}

}
