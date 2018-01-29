<?php
/**
 * Class MySQL
 *
 * @filesource   MySQL.php
 * @created      11.01.2018
 * @package      chillerlan\Database\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Dialects;

class MySQL extends DialectAbstract{

	protected $quotes = ['`', '`'];
	protected $charset = 'utf8mb4_bin';

	/** @inheritdoc */
	public function insert(string $table, array $fields, string $onConflict = null):array{
		$onConflict = strtoupper($onConflict);

		switch($onConflict){
			case 'IGNORE':
				$sql = ['INSERT IGNORE'];
				break;
			case 'REPLACE':
				$sql = ['REPLACE'];
				break;
			default:
				$sql = ['INSERT'];
		}

		$sql[] = 'INTO';
		$sql[] = $this->quote($table);
		$sql[] = '('.$this->quotes[0].implode($this->quotes[1].', '.$this->quotes[0], $fields).$this->quotes[1].')';
		$sql[] = 'VALUES';
		$sql[] = '('.implode(',', array_fill(0, count($fields), '?')).')';

		return $sql;
	}

	/** @inheritdoc */
	public function createDatabase(string $dbname, bool $ifNotExists = null, string $collate = null):array {
		$collate = $collate ?? $this->charset;

		$sql[] = 'CREATE DATABASE';

		if($ifNotExists){
			$sql[] = 'IF NOT EXISTS';
		}

		$sql[] = $this->quote($dbname);

		if(!empty($collate)){
			[$charset] = explode('_', $collate);

			$sql[] = 'CHARACTER SET';
			$sql[] = $charset;

			if($collate !== $charset){
				$sql[] = 'COLLATE';
				$sql[] = $collate;
			}

		}

		return $sql;
	}

	/** @inheritdoc */
	public function createTable(string $table, array $cols, string $primaryKey = null, bool $ifNotExists, bool $temp, string $dir = null):array {
		$sql = ['CREATE'];

		if($temp){
			$sql[] = 'TEMPORARY';
		}

		$sql[] = 'TABLE' ;

		if($ifNotExists){
			$sql[] =  'IF NOT EXISTS' ;
		}

		$sql[] =  $this->quote($table);

		if(!empty($cols)){
			$sql[] = '(';
			$sql[] = implode(', ', $cols);

			if(!empty($primaryKey)){
				$sql[] = ', PRIMARY KEY (';
				$sql[] = $this->quote($primaryKey);
				$sql[] =  ')';
			}

			$sql[] = ')';
		}

		if(!empty($this->charset)){
			[$charset] = explode('_', $this->charset);

			$sql[] = 'CHARACTER SET';
			$sql[] = $charset;

			if($charset !== $this->charset){
				$sql[] = 'COLLATE';
				$sql[] = $this->charset;
			}

		}

		return $sql;
	}

	/** @inheritdoc */
	public function fieldspec(string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null):string {
		$type = strtoupper(trim($type));
		$defaultType = strtoupper($defaultType);


		$field = [$this->quote(trim($name))];

		// @todo: whitelist types?
		$nolengthtypes = ['DATE', 'TINYBLOB', 'TINYTEXT', 'BLOB', 'TEXT', 'MEDIUMBLOB',
		                  'MEDIUMTEXT', 'LONGBLOB', 'LONGTEXT', 'SERIAL', 'BOOLEAN', 'UUID'];

		$field[] = (is_int($length) || (is_string($length) && count(explode(',', $length)) === 2)) && !in_array($type, $nolengthtypes, true)
			? $type.'('. $length . ')'
			: $type;

		if($attribute){
			$field[] = strtoupper($attribute);
		}

		$collationtypes = ['TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT', 'VARCHAR', 'CHAR', 'ENUM', 'SET'];
		if($collation && in_array($type, $collationtypes, true)){
			[$charset] = explode('_', $collation);

			$field[] = 'CHARACTER SET '.$charset;

			if($charset !== $collation){
				$field[] = 'COLLATE '.$collation;
			}

		}

		if($isNull !== null){
			$field[] = $isNull ? 'NULL' : 'NOT NULL';
		}

		if($defaultType === 'USER_DEFINED'){

			switch(true){
				case $type === 'TIMESTAMP' && intval($defaultValue) === 0:
					$field[] = 'DEFAULT 0';
					break;
				case $type === 'BIT':
					$field[] = 'DEFAULT b\''.preg_replace('/[^01]/', '0', $defaultValue).'\'';
					break;
				case $type === 'BOOLEAN':
					$field[] = 'DEFAULT '.preg_match('/^1|T|TRUE|YES$/i', $defaultValue) ? 'TRUE' : 'FALSE';
					break;
				case $type === 'BINARY' || $type === 'VARBINARY':
					$field[] = 'DEFAULT 0x'.$defaultValue;
					break;
				case strtoupper($defaultValue) === 'NULL' && $isNull === true:
					$field[] = 'DEFAULT NULL';
					break;
				default:
					$field[] = 'DEFAULT '.(is_int($defaultValue) || is_float($defaultValue) ? $defaultValue : '\''.$defaultValue.'\'') ;
			}

		}
		else if($defaultType === 'CURRENT_TIMESTAMP'){
			$field[] = 'DEFAULT CURRENT_TIMESTAMP';
		}
		else if($defaultType === 'NULL' && $isNull === true){
			$field[] = 'DEFAULT NULL';
		}


		if($extra){
			$field[] = $extra;
		}

		return implode(' ', $field);
	}

	/** @inheritdoc */
	public function showDatabases():array{
		return ['SHOW DATABASES'];
	}

	/** @inheritdoc */
	public function showTables(string $database = null, string $pattern = null, string $where = null):array{
		return ['SHOW TABLES'];
	}

	/** @inheritdoc */
	public function showCreateTable(string $table):array{
		$sql = ['SHOW CREATE TABLE'];
		$sql[] = $this->quote($table);

		return $sql;
	}

}
