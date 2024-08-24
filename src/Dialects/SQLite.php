<?php
/**
 * Class SQLite
 *
 * @filesource   SQLite.php
 * @created      11.01.2018
 * @package      chillerlan\Database\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Dialects;

final class SQLite extends DialectAbstract{

	/**
	 * @inheritdoc
	 *
	 * @see https://www.sqlite.org/datatype3.html
	 */
	public function fieldspec(string $name, string $type, mixed $length = null, string|null $attribute = null, string|null $collation = null, bool|null $isNull = null, string|null $defaultType = null, mixed $defaultValue = null, string|null $extra = null):string{
		$type      = strtoupper(trim($type));
		$collation = strtoupper($collation ?? '');

		$field = [$this->quote(trim($name))];

		$type_translation = [
			'TINYINT'    => 'INTEGER',
			'SMALLINT'   => 'INTEGER',
			'MEDIUMINT'  => 'INTEGER',
			'BIGINT'     => 'INTEGER',
			'TINYTEXT'   => 'TEXT',
			'MEDIUMTEXT' => 'TEXT',
			'LONGTEXT'   => 'TEXT',
			'DOUBLE'     => 'REAL',
			'DECIMAL'    => 'REAL',
		][$type] ?? $type;

		if($length !== null && (in_array($type, ['CHAR', 'NCHAR', 'VARCHAR', 'NVARCHAR', 'CHARACTER'], true) || (is_string($length) && count(explode(',', $length)) === 2 && $type === 'DECIMAL'))){
			$field[] = $type_translation.'('.$length.')';
		}
		else{
			$field[] = $type_translation;
		}

		if(is_bool($isNull)){
			$field[] = $isNull ? 'NULL' : 'NOT NULL';
		}

		if(in_array($collation, ['BINARY', 'NOCASE', 'RTRIM'], true)){
			$field[] = 'COLLATE '.$collation;
		}

		$defaultType = strtoupper($defaultType ?? '');

		if($defaultType === 'USER_DEFINED'){
			$field[] = 'DEFAULT \''.$defaultValue.'\'';
		}
		elseif(in_array($defaultType, ['CURRENT_DATE', 'CURRENT_TIME', 'CURRENT_TIMESTAMP'], true)){
			$field[] = 'DEFAULT '.$defaultType;
		}
		elseif($defaultType === 'NULL' && $isNull === true){
			$field[] = 'DEFAULT NULL';
		}

		if($attribute){
			$field[] = $attribute;
		}

		if($extra){
			$field[] = $extra;
		}

		return implode(' ', $field);
	}

	/** @inheritdoc */
	public function createTable(string $table, array $cols, string|null $primaryKey = null, bool|null $ifNotExists = null, bool|null $temp = null, string|null $dir = null):array{
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
			$sql[] = '(';
			$sql[] = implode(', ', $cols);

			if($primaryKey){
				$sql[] = ', PRIMARY KEY';
				$sql[] = '(';
				$sql[] = $this->quote($primaryKey);

				if(in_array($dir, ['ASC', 'DESC'], true)){
					$sql[] = ($dir);
				}

				$sql[] = ')';
			}

			$sql[] = ')';
		}

		return $sql;
	}

	/** @inheritdoc */
	public function truncate(string $table):array{
		$sql = ['DELETE FROM'];// ??? sqlite
		$sql[] = $this->quote($table);

		return $sql;
	}

	/** @inheritdoc */
	public function showTables(string|null $database = null, string|null $pattern = null, string|null $where = null):array{
		/** @noinspection SqlResolve */
		return ['SELECT "name" AS "tablename" FROM sqlite_master'];
	}

	/** @inheritdoc */
	public function showCreateTable(string $table):array{
		/** @noinspection SqlResolve */
		$sql = ['SELECT "name" AS "Table", "sql" AS "Create Table" FROM sqlite_master WHERE name ='];
		$sql[] = $this->quote($table);

		return $sql;
	}
}
