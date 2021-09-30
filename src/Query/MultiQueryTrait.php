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

/**
 * @implements \chillerlan\Database\Query\MultiQuery
 */
trait MultiQueryTrait{
	use QueryTrait;

	/** @inheritdoc */
	public function multi(array $values = null):bool{
		$sql    = $this->sql(true);
		$values = $values ?? ($this instanceof BindValues ? $this->getBindValues() : []);

		$this->logger->debug('MultiQueryTrait::multi()', ['method' => __METHOD__, 'sql' => $sql, 'val' => $values]);

		return $this->db->multi($sql, $values);
	}

	/** @inheritdoc */
	public function callback(array $values, callable $callback):bool{
		$sql    = $this->sql(true);

		$this->logger->debug('MultiQueryTrait::callback()', ['method' => __METHOD__, 'sql' => $sql, 'val' => $values]);

		return $this->db->multiCallback($sql, $values, $callback);
	}

}
