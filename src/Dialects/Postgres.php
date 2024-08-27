<?php
/**
 * Class Postgres
 *
 * @filesource   Postgres.php
 * @created      11.01.2018
 * @package      chillerlan\Database\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Dialects;

use chillerlan\Database\Query\QueryException;
use function count, explode, implode, in_array, intval, is_int, is_string,
	preg_match, preg_replace, strtolower, strtoupper, trim;

final class Postgres extends DialectAbstract{

	private const TYPE_TRANSLATION = [
		'TINYINT'    => 'SMALLINT',
		'MEDIUMINT'  => 'INT',
		'DOUBLE'     => 'DOUBLE PRECISION',
		'TINYTEXT'   => 'TEXT',
		'DATETIME'   => 'TIMESTAMP',
		'IMAGE'      => 'BLOB',
		'MEDIUMTEXT' => 'TEXT',
		'LONGTEXT'   => 'TEXT',
	];

	public function select(
		array       $cols,
		array       $from,
		string|null $where = null,
		mixed       $limit = null,
		mixed       $offset = null,
		bool|null   $distinct = null,
		array|null  $groupby = null,
		array|null  $orderby = null,
	):array{
		$sql = ['SELECT'];

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

	/**
	 * @link https://www.postgresql.org/docs/9.5/static/sql-insert.html#SQL-ON-CONFLICT
	 *
	 * @throws \chillerlan\Database\Query\QueryException
	 */
	public function insert(
		string      $table,
		array       $fields,
		string|null $onConflict = null,
		string|null $conflictTarget = null,
	):array{
		$sql = parent::insert($table, $fields);

		if(in_array($onConflict, ['IGNORE', 'REPLACE'], true)){

			if(empty($conflictTarget)){
				throw new QueryException('postgres insert on conflict: no conflict target given');
			}

			$sql[] =  'ON CONFLICT ('.$this->quote($conflictTarget).') DO';

			switch($onConflict){
				case 'IGNORE':
					$sql[] = 'NOTHING';
					break;
				case 'REPLACE':
					$sql[] = $this->onConflictUpdate($fields);
					break;
			}

		}

		return $sql;
	}

	protected function onConflictUpdate(array $fields):string{
		$onConflictUpdate = [];

		foreach($fields as $f){
			$onConflictUpdate[] = $this->quote($f).' = EXCLUDED.'.$this->quote($f);
		}

		return 'UPDATE SET '.implode(', ', $onConflictUpdate);
	}

	public function createDatabase(string $dbname, bool|null $ifNotExists = null, string|null $collate = null):array{
		$sql = ['CREATE DATABASE'];
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

	/**
	 * @see https://www.postgresql.org/docs/9.5/datatype.html
	 */
	public function fieldspec(
		string      $name,
		string      $type,
		mixed       $length = null,
		string|null $attribute = null,
		string|null $collation = null,
		bool|null   $isNull = null,
		string|null $defaultType = null,
		mixed       $defaultValue = null,
		string|null $extra = null,
	):string{
		$name             = trim($name);
		$type             = strtoupper(trim($type));
		$field            = [$this->quote($name)];
		$type_translation = self::TYPE_TRANSLATION[$type] ?? $type;

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

		if($isNull !== true){
			$field[] = 'NOT NULL';
		}

		if($attribute){
			$field[] = strtoupper($attribute);
		}

		$defaultType = strtoupper($defaultType ?? '');

		if($defaultType === 'USER_DEFINED'){

			$field[] = match (true) {
				$type === 'TIMESTAMP' && intval($defaultValue) === 0             => 'DEFAULT 0',
				$type === 'BIT' || $type === 'VARBIT'                            => 'DEFAULT b\''.preg_replace('/[^01]/', '0', $defaultValue).'\'',
				$type === 'BOOLEAN'                                              => 'DEFAULT '.(preg_match('/^1|T|TRUE|YES$/i', $defaultValue) ? 'TRUE' : 'FALSE'),
				strtoupper((string)$defaultValue) === 'NULL' && $isNull === true => 'DEFAULT NULL',
				default                                                          => 'DEFAULT \''.$defaultValue.'\'',
			};

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

	public function createTable(
		string      $table,
		array       $cols,
		string|null $primaryKey = null,
		bool|null   $ifNotExists = null,
		bool|null   $temp = null,
		string|null $dir = null,
	):array{
		$sql = ['CREATE'];

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

	public function showDatabases():array{
		/** @noinspection SqlResolve, SqlDialectInspection */
		return ['SELECT datname AS "Database" FROM pg_database'];
	}

	public function showTables(string|null $database = null, string|null $pattern = null, string|null $where = null):array{
		/** @noinspection SqlResolve, SqlDialectInspection */
		return ['SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname != \'pg_catalog\'AND schemaname != \'information_schema\' '];
	}

}
