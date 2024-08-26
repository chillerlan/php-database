<?php
/**
 * Class Firebird
 *
 * @filesource   Firebird.php
 * @created      11.01.2018
 * @package      chillerlan\Database\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Dialects;

use function count, explode, implode, in_array, intval, preg_match, strtolower, strtoupper, trim;

final class Firebird extends DialectAbstract{

	private const TYPE_TRANSLATION =  [
		'TINYINT'    => 'SMALLINT',
		'MEDIUMINT'  => 'INT',
		'BIGINT'     => 'INT64',
		'REAL'       => 'DOUBLE PRECISION',
		'DOUBLE'     => 'DOUBLE PRECISION',
		'BOOLEAN'    => 'CHAR(1)',
		'BINARY'     => 'CHAR',
		'VARBINARY'  => 'CHAR',
		'TINYTEXT'   => 'VARCHAR(255)',
		'DATETIME'   => 'TIMESTAMP',
		'IMAGE'      => 'BLOB',
		'TEXT'       => 'BLOB SUB_TYPE TEXT',
		'MEDIUMTEXT' => 'BLOB SUB_TYPE TEXT',
		'LONGTEXT'   => 'BLOB SUB_TYPE TEXT',
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

		if($limit !== null){
			$sql[] = 'FIRST ? SKIP ?';
		}

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

		return $sql;
	}

	public function createTable(
		string      $table,
		array       $cols,
		string|null $primaryKey = null,
		bool|null   $ifNotExists = null,
		bool|null   $temp = null,
		string|null $dir = null,
	):array{
		$sql = [$ifNotExists ? 'RECREATE' : 'CREATE']; // nasty

		if($temp){
			$sql[] = 'GLOBAL TEMPORARY';
		}

		$sql[] = 'TABLE';

		$n = explode('.', $table);

		$sql[] = $this->quote($n[count($n) - 1]);

		$_cols = [];

		if(!empty($cols)){

			foreach($cols as $name => $col){

				if(strtolower($name) === strtolower($primaryKey ?? '')){
					$x = explode(' NOT NULL', $col, 2);

					if(count($x) > 0){
						$col = $x[0].' NOT NULL PRIMARY KEY';
						$col .= $x[1] ?? '';
					}

				}

				$_cols[] = $col;
			}

			$sql[] = '('.implode(', ', $_cols).')';
		}

		return $sql;
	}

	public function dropTable(string $table, bool $ifExists):array{
/*
		if($ifExists){
			$sql[] = '
EXECUTE BLOCK AS  BEGIN
    IF  ( EXISTS ( SELECT 1 FROM RDB$RELATIONS WHERE RDB$RELATION_NAME = '.$this->quote($table).' ))
    THEN  BEGIN
        EXECUTE STATEMENT \'DROP TABLE '.$this->quote($table).';\' ;
    END
END ^
';
		}
		else{

		}
*/
			$sql = ['DROP TABLE'];
			$sql[] = $this->quote($table);


		return $sql;
	}

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
		$type  = strtoupper(trim($type));
		$field = [$this->quote(trim($name))];

		$field[] = match(true){
			isset(self::TYPE_TRANSLATION[$type])                             => self::TYPE_TRANSLATION[$type],
			in_array($type, ['CHAR', 'VARCHAR', 'DECIMAL', 'NUMERIC'], true) => $type.'('.$length.')',
			default                                                          => $type,
		};

		if($isNull === false && !in_array($type, ['DATE', 'TIME', 'TIMESTAMP'], true)){
			$field[] = 'NOT NULL';
		}

		$defaultType = strtoupper($defaultType ?? '');

		if($defaultType === 'USER_DEFINED'){

			$field[] = match(true){
				$type === 'TIMESTAMP' && intval($defaultValue) === 0     => 'DEFAULT 0',
				strtoupper($defaultValue) === 'NULL' && $isNull === true => 'DEFAULT NULL',
				$type === 'BOOLEAN'                                      => 'DEFAULT '.(preg_match('/^1|T|TRUE|YES$/i', $defaultValue) ? '1' : '0'),
				default                                                  => 'DEFAULT \''.$defaultValue.'\'',
			};

		}
		elseif($defaultType === 'CURRENT_TIMESTAMP'){
			$field[] = 'DEFAULT CURRENT_TIMESTAMP';
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

	public function truncate(string $table):array{
		$sql = ['DELETE FROM'];// RECREATE TABLE [table spec] ...stupid firebird 2.5
		$sql[] = $this->quote($table);

		return $sql;
	}

	public function showDatabases():array{
		/** @noinspection SqlResolve, SqlDialectInspection */
		return ['SELECT TRIM(LOWER(MON$DATABASE_NAME)) AS "Database" FROM MON$DATABASE'];
	}


	public function showTables(string|null $database = null, string|null $pattern = null, string|null $where = null):array{
		/** @noinspection SqlResolve, SqlDialectInspection */
		return ['SELECT TRIM(RDB$RELATION_NAME) AS "tablename" FROM RDB$RELATIONS WHERE RDB$VIEW_BLR IS NULL AND (RDB$SYSTEM_FLAG IS NULL OR RDB$SYSTEM_FLAG = 0)'];
	}

}
