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

namespace chillerlan\Database\Dialects;

use chillerlan\Database\Query\QueryException;

final class Postgres extends DialectAbstract{

	/** @inheritdoc */
	public function select(array $cols, array $from, string $where = null, $limit = null, $offset = null, bool $distinct = null, array $groupby = null, array $orderby = null):array{
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
	 * @inheritdoc
	 */
	public function insert(string $table, array $fields, string $onConflict = null, string $conflictTarget = null):array{
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

	/**
	 * @param array $fields
	 *
	 * @return string
	 */
	protected function onConflictUpdate(array $fields):string {
		$onConflictUpdate = [];

		foreach($fields as $f){
			$onConflictUpdate[] = $this->quote($f).' = EXCLUDED.'.$this->quote($f);
		}

		return 'UPDATE SET '.implode(', ', $onConflictUpdate);
	}

	/** @inheritdoc */
	public function createDatabase(string $dbname, bool $ifNotExists = null, string $collate = null):array{
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

		if($isNull !== true){
			$field[] = 'NOT NULL';
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
					$field[] = 'DEFAULT '.(preg_match('/^1|T|TRUE|YES$/i', $defaultValue) ? 'TRUE' : 'FALSE');
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
	public function createTable(string $table, array $cols, string $primaryKey = null, bool $ifNotExists = null, bool $temp = null, string $dir = null):array{
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

	/** @inheritdoc */
	public function showDatabases():array{
		/** @noinspection SqlResolve */
		return ['SELECT datname AS "Database" FROM pg_database'];
	}

	/** @inheritdoc */
	public function showTables(string $database = null, string $pattern = null, string $where = null):array{
		/** @noinspection SqlResolve */
		return ['SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname != \'pg_catalog\'AND schemaname != \'information_schema\' '];
	}

	/**
	 * @link https://stackoverflow.com/a/16154183
	 *
	 * @param string $table
	 *
	 * @return array
	 * @noinspection SqlResolve
	 */
/*	public function showCreateTable(string $table):array{

		$def = $this->db->prepared('SELECT
				a.attnum AS "id",
				a.attname AS "name",
				pg_catalog.format_type(a.atttypid, a.atttypmod) AS "type",
				CASE WHEN a.attnotnull = TRUE
					THEN \'NOT NULL\'
				ELSE \'\' END AS "isnull",
				CASE WHEN (
					SELECT substring(pg_catalog.pg_get_expr(d.adbin, d.adrelid) FOR 128)
					FROM pg_catalog.pg_attrdef d
					WHERE
						d.adrelid = a.attrelid
						AND d.adnum = a.attnum
						AND a.atthasdef
				) IS NOT NULL
					THEN \'DEFAULT \' || (
						SELECT substring(pg_catalog.pg_get_expr(d.adbin, d.adrelid) FOR 128)
						FROM pg_catalog.pg_attrdef d
						WHERE
							d.adrelid = a.attrelid
							AND d.adnum = a.attnum
							AND a.atthasdef
					)
				ELSE \'\' END AS "default",
				(
					SELECT collation_name
					FROM information_schema.columns
					WHERE
						columns.table_name = b.relname
						AND columns.column_name = a.attname
				) AS "collation",
				(
					SELECT c.relname
					FROM pg_catalog.pg_class AS c, pg_attribute AS at, pg_catalog.pg_index AS i, pg_catalog.pg_class c2
					WHERE
						c.relkind = \'i\'
						AND at.attrelid = c.oid
						AND i.indexrelid = c.oid
						AND i.indrelid = c2.oid
						AND c2.relname = b.relname
						AND at.attnum = a.attnum
				) AS "index"
			FROM
				pg_catalog.pg_attribute AS a
				INNER JOIN
				(
					SELECT
						c.oid,
						n.nspname,
						c.relname
					FROM pg_catalog.pg_class AS c, pg_catalog.pg_namespace AS n
					WHERE
						pg_catalog.pg_table_is_visible(c.oid)
						AND n.oid = c.relnamespace
						AND c.relname = ?
					ORDER BY 2, 3) b
					ON a.attrelid = b.oid
				INNER JOIN
				(
					SELECT a.attrelid
					FROM pg_catalog.pg_attribute a
					WHERE
						a.attnum > 0
						AND NOT a.attisdropped
					GROUP BY a.attrelid
				) AS e
					ON a.attrelid = e.attrelid
			WHERE a.attnum > 0
			      AND NOT a.attisdropped
			ORDER BY a.attnum', [$table]);

		foreach($def as $field){
			// @todo primary key/indices
			$fields[] = $this->fieldspec(trim($field->name), trim($field->type), null, null, null, $field->isnull !== 'NOT NULL', null, null, trim($field->default));
		}

		$create = sprintf('CREATE TABLE %1$s (%2$s)', $this->quote($table), PHP_EOL.implode(','.PHP_EOL, $fields).PHP_EOL);

		$this->db->prepared('CREATE TEMPORARY TABLE IF NOT EXISTS TEMP$SQL_CREATE ("name" TEXT, "create" TEXT) ON COMMIT PRESERVE ROWS');
		$this->db->prepared('TRUNCATE TEMP$SQL_CREATE');
		$this->db->prepared('INSERT INTO TEMP$SQL_CREATE ("name", "create") VALUES (?, ?)', [$table, $create]);

		return ['SELECT "name" AS "Table", "create" AS "Create Table" FROM TEMP$SQL_CREATE'];
	}
*/
}
