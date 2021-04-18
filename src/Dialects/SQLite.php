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

class SQLite extends DialectAbstract{

	/** @inheritdoc */
	public function fieldspec(string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null):string{
		$type      = strtoupper(trim($type));
		$collation = strtoupper($collation);

		$field = [$this->quote(trim($name))];

		$type_translation = [
			'MEDIUMTEXT' => 'TEXT',
			'LONGTEXT'   => 'TEXT',
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

		$defaultType = strtoupper($defaultType);

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
	public function createTable(string $table, array $cols, string $primaryKey = null, bool $ifNotExists = null, bool $temp = null, string $dir = null):array{
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
	public function showTables(string $database = null, string $pattern = null, string $where = null):array{
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
