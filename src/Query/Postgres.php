<?php
/**
 * Class Postgres
 *
 * @filesource   Postgres.php
 * @created      11.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

class Postgres extends DialectAbstract{

	protected $quotes = ['"', '"'];

	/** @inheritdoc */
	public function select(array $from, string $where = null, $limit = null, $offset = null, bool $distinct = null, array $groupby, array $orderby):array{
		$sql[] = 'SELECT';

		if($distinct){
			$sql[] = 'DISTINCT';
		}

		!empty($cols)
			? $sql[] = implode(', ', $cols)
			: $sql[] = '*';

		$sql[] = 'FROM';
		$sql[] = implode(', ', $from);
		$sql[] = $where;

		if(!empty($groupby)){
			$sql[] = 'GROUP BY';
			$sql[] = implode(', ', $groupby);
		}

		if(!empty($orderby)){
			$sql[] = 'ORDER BY';
			$sql[] = implode(', ', $orderby);
		}

		if($offset !== null){
			$sql[] = 'OFFSET ?';
		}

		if($limit !== null){
			$sql[] = 'LIMIT ?';
		}

		return $sql;
	}

	/** @inheritdoc */
	public function createDatabase(string $dbname, bool $ifNotExists = null, string $collate = null):array{
		$sql[] = 'CREATE DATABASE';
		$sql[] = $this->quote($dbname);

		if($collate){
			$charset = explode(',', $collate, 3);

			$count = count($charset);

			if($count > 0){
				$sql[] = 'ENCODING \''.strtoupper($charset[0]).'\'';
			}

			if($count > 1){
				$sql[] = 'LC_COLLATE=\''.$charset[1].'\'';
			}

			if($count > 2){
				$sql[] = 'LC_CTYPE=\''.$charset[2].'\'';
			}

		}

		return $sql;
	}

	/** @inheritdoc */
	public function fieldspec(string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null):string{
		$name = trim($name);
		$type = strtoupper(trim($type));

		$field = [$this->quote($name)];

		$type_translation = [
			'TINYINT'    => 'SMALLINT',
			'MEDIUMINT'  => 'INT',
			'DOUBLE'     => 'DOUBLE PRECISION',
			'TINYTEXT'   => 'VARCHAR(255)',
			'DATETIME'   => 'TIMESTAMP',
			'IMAGE'      => 'BLOB',
			'MEDIUMTEXT' => 'TEXT',
			'LONGTEXT'   => 'TEXT',
		][$type] ?? $type;

		if((is_int($length) || is_string($length) && count(explode(',', $length)) === 2)
		   && in_array($type, ['BIT', 'VARBIT', 'CHAR', 'VARCHAR', 'DECIMAL', 'NUMERIC', 'TIME', 'TIMESTAMP', 'INTERVAL'], true)){
			$field[] = $type_translation.'('.$length.')';
		}
		else{
			$field[] = $type_translation;
		}

		if($collation && in_array($type, ['TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT', 'VARCHAR', 'CHAR'], true)
		   && !in_array(strtolower($collation), ['utf8'], true)){
			$field[] = 'COLLATE '.$collation;
		}

		if(is_bool($isNull)){
			$field[] = $isNull ? 'NULL' : 'NOT NULL';
		}

		if($attribute){
			$field[] = strtoupper($attribute);
		}

		$defaultType = strtoupper($defaultType);

		if($defaultType === 'USER_DEFINED'){

			switch(true){
				case $type === 'TIMESTAMP' && intval($defaultValue) === 0:
					$field[] = 'DEFAULT 0';
					break;
				case $type === 'BIT' || $type === 'VARBIT':
					$field[] = 'DEFAULT b\''.preg_replace('/[^01]/', '0', $defaultValue).'\'';
					break;
				case $type === 'BOOLEAN':
					$field[] = 'DEFAULT '.preg_match('/^1|T|TRUE|YES$/i', $defaultValue) ? 'TRUE' : 'FALSE';
					break;
				case strtoupper($defaultValue) === 'NULL' && $isNull === true:
					$field[] = 'DEFAULT NULL';
					break;
				default:
					$field[] = 'DEFAULT \''.$defaultValue.'\'';
			}

		}
		elseif($defaultType === 'CURRENT_TIMESTAMP'){
			$field[] = 'DEFAULT CURRENT_TIMESTAMP';
		}
		elseif($defaultType === 'NULL' && $isNull === true){
			$field[] = 'DEFAULT NULL';
		}

		if($extra){
			$field[] = $extra;
		}

		return implode(' ', $field);
	}

	/** @inheritdoc */
	public function createTable(string $table, array $cols, string $primaryKey = null, bool $ifNotExists, bool $temp, string $dir = null):array{
		$sql[] = 'CREATE';

		if($temp){
			$sql[] = 'TEMPORARY';
		}

		$sql[] = 'TABLE';

		if($ifNotExists){
			$sql[] = 'IF NOT EXISTS';
		}

		$n = explode('.', $table);

		$sql[] = $this->quote($n[count($n) - 1]);

		if(!empty($cols)){
			$sql[] = ' (';
			$sql[] = implode(', ', $cols);

			if($primaryKey){
				$sql[] = ','.'PRIMARY KEY ('.$this->quote($primaryKey).')';
			}

			$sql[] = ')';
		}

		return $sql;
	}

}
