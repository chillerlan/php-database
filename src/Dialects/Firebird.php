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

use chillerlan\Database\ResultInterface;

class Firebird extends DialectAbstract{

	protected $quotes = ['"', '"'];

	/** @inheritdoc */
	public function select(array $cols, array $from, string $where = null, $limit = null, $offset = null, bool $distinct = null, array $groupby = null, array $orderby = null):array{
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

	/** @inheritdoc */
	public function createTable(string $table, array $cols, string $primaryKey = null, bool $ifNotExists = null, bool $temp = null, string $dir = null):array{
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

				if(strtolower($name) === strtolower($primaryKey)){
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

	/** @inheritdoc */
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

	/** @inheritdoc */
	public function fieldspec(string $name, string $type, $length = null, string $attribute = null, string $collation = null, bool $isNull = null, string $defaultType = null, $defaultValue = null, string $extra = null):string{
		$type = strtoupper(trim($type));

		$field = [$this->quote(trim($name))];

		$type_translation = [
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
		][$type] ?? false;

		if($type_translation){
			$field[] = $type_translation;
		}
		elseif(in_array($type, ['CHAR', 'VARCHAR', 'DECIMAL', 'NUMERIC'], true)){
			$field[] = $type.'('.$length.')';
		}
		else{
			$field[] = $type;
		}

		if($isNull === false && !in_array($type, ['DATE', 'TIME', 'TIMESTAMP'], true)){
			$field[] = 'NOT NULL';
		}

		$defaultType = strtoupper($defaultType);

		if($defaultType === 'USER_DEFINED'){

			switch(true){
				case $type === 'TIMESTAMP' && intval($defaultValue) === 0:
					$field[] = 'DEFAULT 0';
					break;
				case strtoupper($defaultValue) === 'NULL' && $isNull === true:
					$field[] = 'DEFAULT NULL';
					break;
				case $type === 'BOOLEAN':
					$field[] = 'DEFAULT '.(preg_match('/^1|T|TRUE|YES$/i', $defaultValue) ? '1' : '0');
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

		if($attribute){
			$field[] = $attribute;
		}

		if($extra){
			$field[] = $extra;
		}

		return implode(' ', $field);
	}

	/** @inheritdoc */
	public function truncate(string $table):array{
		$sql = ['DELETE FROM'];// RECREATE TABLE [table spec] ...stupid firebird 2.5
		$sql[] = $this->quote($table);

		return $sql;
	}

	/** @inheritdoc */
	public function showDatabases():array{
		/** @noinspection SqlResolve */
		return ['SELECT TRIM(LOWER(MON$DATABASE_NAME)) AS "Database" FROM MON$DATABASE'];
	}


	/** @inheritdoc */
	public function showTables(string $database = null, string $pattern = null, string $where = null):array{
		/** @noinspection SqlResolve */
		return ['SELECT TRIM(RDB$RELATION_NAME) AS "tablename" FROM RDB$RELATIONS WHERE RDB$VIEW_BLR IS NULL AND (RDB$SYSTEM_FLAG IS NULL OR RDB$SYSTEM_FLAG = 0)'];
	}

	/**
	 * this is such a hack. i hate firebird so much.
	 *
	 * @link https://stackoverflow.com/a/12074601
	 * @param string $table
	 *
	 * @return array
	 */
	public function showCreateTable(string $table):array{

		/** @noinspection SqlResolve */
		$def = $this->db->prepared('
			SELECT 
				RF.RDB$FIELD_POSITION AS "id", 
				TRIM(RF.RDB$FIELD_NAME) AS "name",
				(CASE F.RDB$FIELD_TYPE
					WHEN 7 THEN
						CASE F.RDB$FIELD_SUB_TYPE
							WHEN 0 THEN \'SMALLINT\'
							WHEN 1 THEN \'NUMERIC(\' || F.RDB$FIELD_PRECISION || \',\' || (-F.RDB$FIELD_SCALE) || \')\'
							WHEN 2 THEN \'DECIMAL(\' || F.RDB$FIELD_PRECISION || \',\' || (-F.RDB$FIELD_SCALE) || \')\'
						END
					WHEN 8 THEN
						CASE F.RDB$FIELD_SUB_TYPE
							WHEN 0 THEN \'INTEGER\'
							WHEN 1 THEN \'NUMERIC(\'  || F.RDB$FIELD_PRECISION || \',\' || (-F.RDB$FIELD_SCALE) || \')\'
							WHEN 2 THEN \'DECIMAL(\'  || F.RDB$FIELD_PRECISION || \',\' || (-F.RDB$FIELD_SCALE) || \')\'
						END
					WHEN 9 THEN \'QUAD\'
					WHEN 10 THEN \'FLOAT\'
					WHEN 12 THEN \'DATE\'
					WHEN 13 THEN \'TIME\'
					WHEN 14 THEN \'CHAR(\' || (TRUNC(F.RDB$FIELD_LENGTH / CH.RDB$BYTES_PER_CHARACTER)) || \') \'
					WHEN 16 THEN
						CASE F.RDB$FIELD_SUB_TYPE
							WHEN 0 THEN \'BIGINT\'
							WHEN 1 THEN \'NUMERIC(\' || F.RDB$FIELD_PRECISION || \', \' || (-F.RDB$FIELD_SCALE) || \')\'
							WHEN 2 THEN \'DECIMAL(\' || F.RDB$FIELD_PRECISION || \', \' || (-F.RDB$FIELD_SCALE) || \')\'
						END
					WHEN 27 THEN \'DOUBLE\'
					WHEN 35 THEN \'TIMESTAMP\'
					WHEN 37 THEN \'VARCHAR(\' || (TRUNC(F.RDB$FIELD_LENGTH / CH.RDB$BYTES_PER_CHARACTER)) || \')\'
					WHEN 40 THEN \'CSTRING\' || (TRUNC(F.RDB$FIELD_LENGTH / CH.RDB$BYTES_PER_CHARACTER)) || \')\'
					WHEN 45 THEN \'BLOB_ID\'
					WHEN 261 THEN \'BLOB SUB_TYPE \' || F.RDB$FIELD_SUB_TYPE
					ELSE \'RDB$FIELD_TYPE: \' || F.RDB$FIELD_TYPE || \'?\'
				END) AS "type",
				IIF(COALESCE(RF.RDB$NULL_FLAG, 0) = 0, NULL, \'NOT NULL\') AS "isnull",
				COALESCE(RF.RDB$DEFAULT_SOURCE, F.RDB$DEFAULT_SOURCE) AS "default",
				TRIM(CH.RDB$CHARACTER_SET_NAME) AS "charset",
				TRIM(DCO.RDB$COLLATION_NAME) AS "collation",
				TRIM(F.RDB$VALIDATION_SOURCE) AS "check",
				TRIM(RF.RDB$DESCRIPTION) AS "desc"
			FROM RDB$RELATION_FIELDS RF
				JOIN RDB$FIELDS F ON (F.RDB$FIELD_NAME = RF.RDB$FIELD_SOURCE)
				LEFT OUTER JOIN RDB$CHARACTER_SETS CH ON (CH.RDB$CHARACTER_SET_ID = F.RDB$CHARACTER_SET_ID)
				LEFT OUTER JOIN RDB$COLLATIONS DCO ON ((DCO.RDB$COLLATION_ID = F.RDB$COLLATION_ID) AND (DCO.RDB$CHARACTER_SET_ID = F.RDB$CHARACTER_SET_ID))
			WHERE (RF.RDB$RELATION_NAME = ?) AND (COALESCE(RF.RDB$SYSTEM_FLAG, 0) = 0)
			ORDER BY RF.RDB$FIELD_POSITION
			', [$table]);

		/** @noinspection SqlResolve */
		$idx = $this->db->prepared('SELECT S.RDB$FIELD_POSITION AS "pos", S.RDB$FIELD_NAME AS "field", I.RDB$INDEX_ID AS "id", I.RDB$UNIQUE_FLAG AS "unique" FROM RDB$INDEX_SEGMENTS AS S, RDB$INDICES AS I WHERE S.RDB$INDEX_NAME = I.RDB$INDEX_NAME AND I.RDB$RELATION_NAME = ?', [$table]);

		$fields = [];
		if($def instanceof ResultInterface && $def->length > 0){

			foreach($def as $field){
				$index = $idx[$field->id]['id'] ?? false;

				if($index){
					$index = $index === 1 ? 'PRIMARY KEY' : 'UNIQUE'; // @todo
				}

				$fields[] = $this->fieldspec(trim($field->name), trim($field->type), null, null, null, $field->isnull !== 'NOT NULL', null, null, trim($field->default.$index));
			}

		}

		$this->db->prepared('RECREATE GLOBAL TEMPORARY TABLE TEMP$SQL_CREATE ("name" BLOB SUB_TYPE TEXT CHARACTER SET UTF8 NOT NULL, "create" BLOB SUB_TYPE TEXT CHARACTER SET UTF8 NOT NULL) ON COMMIT PRESERVE ROWS');

		$create = sprintf(/** @lang text */'CREATE TABLE %1$s (%2$s)', $this->quote($table), PHP_EOL.implode(','.PHP_EOL, $fields).PHP_EOL);

		/** @noinspection SqlResolve */
		$this->db->prepared('INSERT INTO TEMP$SQL_CREATE ("name", "create") VALUES (?, ?)', [$table, $create]);

		/** @noinspection SqlResolve */
		return ['SELECT "name" AS "Table", "create" AS "Create Table" FROM TEMP$SQL_CREATE'];
	}

}
