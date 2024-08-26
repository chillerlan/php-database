<?php
/**
 * Class MSSQL
 *
 * @filesource   MSSQL.php
 * @created      11.01.2018
 * @package      chillerlan\Database\Dialects
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database\Dialects;

use function count, explode, implode, in_array, is_int, is_string, preg_match, strtolower, strtoupper, trim;

final class MSSQL extends DialectAbstract{

	private const TYPE_TRANSLATION = [
		'boolean'    => 'bit',
		'bool   '    => 'bit',
		'mediumint'  => 'int',
		'double'     => 'float',
		'tinytext'   => 'text',
		'mediumtext' => 'text',
		'longtext'   => 'text',
		'timestamp'  => 'datetime2',
	];

	protected array $quotes = ['[', ']'];

	public function dropTable(string $table, bool $ifExists):array{
		// @todo: if exists
		$sql = ['DROP TABLE'];

		$sql[] = $this->quote($table);

		return $sql;
	}

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

		if($limit !== null){

			if(empty($orderby)){
				$sql[] = 'ORDER BY 1';
			}

			$sql[] = 'OFFSET ? ROWS FETCH NEXT ? ROWS ONLY';
		}

		return $sql;
	}

	public function createDatabase(string $dbname, bool|null $ifNotExists = null, string|null $collate = null):array{
		$sql = ['CREATE DATABASE'];
		$sql[] = $this->quote($dbname);

		if($collate){
			$sql[] = 'COLLATE';
			$sql[] = $collate;
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
		$sql = [];
		// @todo
#		if($ifNotExists){
#			$sql[] = 'IF OBJECT_ID(N\''.str_replace(['[', ']'], '', $table).'\', N\'U\') IS NULL' ;
#		}

		$sql[] = 'CREATE TABLE';
		$sql[] = $this->quote($table);

		if(!empty($cols)){
			$sql[] = '(';
			$sql[] = implode(',', $cols);

			if($primaryKey){
				$sql[] = ', PRIMARY KEY ('.$this->quote($primaryKey).')';
			}

			$sql[] = ')';
		}

		return $sql;
	}

	/**
	 * @see https://docs.microsoft.com/sql/t-sql/data-types/numeric-types
	 * @see https://docs.microsoft.com/sql/t-sql/data-types/string-and-binary-types
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
		$type             = strtolower(trim($type));
		$field            = [$this->quote(trim($name))];
		$type_translation = self::TYPE_TRANSLATION[$type] ?? $type;

		if((is_int($length) || is_string($length) && (count(explode(',', $length)) === 2 || $length === 'max'))
		   && in_array($type, ['char', 'varchar', 'nchar', 'nvarchar', 'decimal', 'numeric', 'datetime2', 'time'], true)){
			$field[] = $type_translation.'('.$length.')';
		}
		else{
			$field[] = $type_translation;
		}

		if($isNull !== null){
			$field[] = $isNull ? 'NULL' : 'NOT NULL';
		}

		$defaultType = strtoupper($defaultType ?? '');

		if($defaultType === 'USER_DEFINED'){

			// @todo
			$field[] = match (true) {
				$type_translation === 'bit' => 'DEFAULT '.(preg_match('/^1|T|TRUE|YES$/i', $defaultValue) ? '1' : '0'),
				default                     => 'DEFAULT \''.$defaultValue.'\'',
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

	public function showDatabases():array{
		// https://stackoverflow.com/questions/147659/get-list-of-databases-from-sql-server
		// EXEC sp_databases
		/** @noinspection SqlResolve, SqlDialectInspection */
		return ['SELECT name AS [Database] FROM master.dbo.sysdatabases WHERE name NOT IN (\'master\', \'tempdb\', \'model\', \'msdb\')'];
	}

	public function showTables(string|null $database = null, string|null $pattern = null, string|null $where = null):array{
		/** @noinspection SqlResolve, SqlDialectInspection */
		return ['SELECT Distinct TABLE_NAME FROM information_schema.TABLES'];
	}

}
