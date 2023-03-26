<?php
/**
 * Class MonologHandler
 *
 * @created      17.11.2021
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2021 smiley
 * @license      MIT
 */

namespace chillerlan\Database;

use chillerlan\Database\Drivers\DriverException;
use InvalidArgumentException;
use Monolog\Formatter\{FormatterInterface, ScalarFormatter};
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Throwable;
use function strpos, trim;

/**
 * Handler class for Monolog that writes log records to the given DB instance.
 *
 * IMPORTANT: Do NOT inject a DB logger into the same DB instance the logger is using - infinite logging events will happen.
 *            (logging DB errors/events to the database is not a great idea in general...)
 */
class MonologHandler extends AbstractProcessingHandler{

	protected Database $db;
	protected string   $logTable;

	/**
	 * MonologHandler constructor
	 */
	public function __construct(Database $db, string $logTable, $level = Level::Debug, bool $bubble = true){
		parent::__construct($level, $bubble);

		$this->db = $db;
		$this->db->connect();

		$this->setTable($logTable);
		$this->formatter = $this->getRecordFormatter();
	}

	/**
	 * @inheritDoc
	 */
	protected function write(LogRecord $record):void{
		$this->db->insert
			->into($this->logTable)
			->values($record->formatted)
			->query()
		;
	}

	/**
	 * Returns a formatter instance
	 */
	protected function getRecordFormatter():FormatterInterface{
		return new class() extends ScalarFormatter{

			public function format(LogRecord $record):array{
				$result = [];

				foreach($record->toArray() as $key => $value){
					if($key === 'level'){
						continue;
					}
					elseif($key === 'datetime'){
						$result[$key] = $value->getTimestamp();
					}
					else{
						$result[$key] = empty($value) ? '' : $this->normalizeValue($value);
					}
				}

				return $result;
			}

		};
	}

	/**
	 * Sets the log table and attempts to create it in case it doesn't exist in the database
	 *
	 * @throws \Throwable
	 */
	protected function setTable(string $logTable):void{
		$logTable = trim($logTable);

		if(empty($logTable)){ // @todo: regex check?
			throw new InvalidArgumentException('invalid log table');
		}

		$this->logTable = $logTable;

		// attempt to create the table
		try{
			$this->db->select->from([$this->logTable])->count();

			return;
		}
		catch(Throwable $e){

			// @todo: check messages for other drivers
			if(!$e instanceof DriverException || strpos($e->getMessage(), 'doesn\'t exist') === false){
				throw $e;
			}

			$this->db->create
				->table($this->logTable)
				->ifNotExists()
				->primaryKey('id')
				->bigint('id', 20, null, false, 'UNSIGNED AUTO_INCREMENT')
				->tinytext('channel')
				->enum('level_name', ['DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'])
				->text('message')
				->text('context', null, true)
				->text('extra', null, true)
				->int('datetime', 10, null, false, 'UNSIGNED')
				->query()
			;
		}

	}

}
