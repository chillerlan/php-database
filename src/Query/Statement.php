<?php
/**
 * Interface Statement
 *
 * @created      27.08.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Query;

use Psr\Log\LoggerInterface;

/**
 *
 */
interface Statement{

	/**
	 * Sets a logger instance on the object.
	 */
	public function setLogger(LoggerInterface $logger):static;

}
