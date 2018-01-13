<?php
/**
 * Interface Statement
 *
 * @filesource   Statement.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use chillerlan\Database\Drivers\DriverInterface;
use Psr\Log\LoggerInterface;

interface Statement{

	/**
	 * Statement constructor.
	 *
	 * @param \chillerlan\Database\Drivers\DriverInterface $db
	 * @param \Psr\Log\LoggerInterface|null                $logger
	 */
	public function __construct(DriverInterface $db, LoggerInterface $logger = null);

}
