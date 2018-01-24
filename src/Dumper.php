<?php
/**
 * Class Dumper
 *
 * @filesource   Dumper.php
 * @created      07.01.2018
 * @package      chillerlan\Database
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database;

use chillerlan\Traits\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @todo WIP
 */
class Dumper extends DatabaseAbstract{

	const PAGESIZE = 100;

	/**
	 * @var \chillerlan\Database\Dialects\Dialect
	 */
	protected $dialect;

	public function __construct(ContainerInterface $options, CacheInterface $cache = null, LoggerInterface $logger = null){
		parent::__construct($options, $cache, $logger);

		$this->dialect = $this->driver->getDialect();
		$this->driver->connect();
	}

	public function dump(array $selection = null, array $not = null){
		$tables = $this->parseTables($selection, $not);

		$fh = fopen($this->options->storage_path.'/dump.sql', 'w');

		foreach($tables as $table){
			$this->info('dumping table: '.$table);
			fwrite($fh, PHP_EOL.'--'.PHP_EOL.'-- '.$table.PHP_EOL.'--'.PHP_EOL.PHP_EOL.$this->query->show->create->table($table)->query()[0]->{'Create Table'}.';'.PHP_EOL.PHP_EOL);

			$q = $this->query->select->from([$table]);
			$pages = (int)floor($q->count() / $this::PAGESIZE);

			foreach(range(0, $pages) as $i){

				$data = (clone $q)
					->limit($this::PAGESIZE)
					->offset($i * $this::PAGESIZE)
					->query();


				if($data instanceof Result && $data->length > 0){
					$sql = ['INSERT INTO'];
					$sql[] = $this->dialect->quote($table);
					$sql[] = '('.implode(', ', array_map([$this->dialect, 'quote'], $data[0]->__fields())).')';
					$sql[] = 'VALUES'.PHP_EOL;

					/** @var \chillerlan\Database\ResultRow $row */
					$values = [];
					foreach($data as $row){
						$values[] = '('.implode(', ', array_map([$this->driver, 'escape'], $row->__values())).')';
					}

					$sql[] = implode(','.PHP_EOL, $values).';'.PHP_EOL.PHP_EOL;

					fwrite($fh, implode(' ', $sql));
				}


				if($i > 0){
					$this->info('dumping data: '.round((100 / $pages) * $i, 2).'%');
				}

			}

		}


	}


	protected function parseTables(array $tables = null, array $not = null):array{
		$q  = $this->query->show->tables()->query()->__toArray();
		$r  = [];
		$st = [];

		foreach($q as $t){
			[$table] = array_values($t);
			$st[] = $table;
		}

		if(empty($tables) && empty($not)){
			return $st;
		}

		foreach($tables as $expression){
			$x = explode('*', $expression, 2);

			foreach($st as $table){

				if(count($x) === 2){

					if(strpos($table, $x[0]) === 0 && !in_array($table, $not, true)){
						$r[] = $table;
					}

				}
				elseif($expression === $table){
					$r[] = $table;
				}

			}

		}

		return $r;
	}
}
