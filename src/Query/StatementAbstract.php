<?php
/**
 * Class StatementAbstract
 *
 * @filesource   StatementAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

use chillerlan\Database\Drivers\DriverInterface;
use chillerlan\Logger\LogTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

abstract class StatementAbstract implements Statement, LoggerAwareInterface{
	use LogTrait;

	/**
	 * @var \chillerlan\Database\Drivers\DriverInterface
	 */
	protected $db;

	/**
	 * @var \chillerlan\Database\Query\Dialect
	 */
	protected $dialect;

	/** @inheritdoc */
	public function __construct(DriverInterface $db, LoggerInterface $logger = null){
		$this->db      = $db;
		$this->log     = $logger;
		$this->dialect = $this->db->getDialect();
	}

}
