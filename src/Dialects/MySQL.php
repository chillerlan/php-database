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

use function array_fill, count, explode, implode, in_array, intval, is_int, is_string,
	preg_match, preg_replace, sodium_bin2hex, strtoupper, trim;

final class MySQL extends DialectAbstract{

	private const nolengthtypes = [
		'DATE', 'TINYBLOB', 'TINYTEXT', 'BLOB', 'TEXT', 'MEDIUMBLOB',
		'MEDIUMTEXT', 'LONGBLOB', 'LONGTEXT', 'SERIAL', 'BOOLEAN', 'UUID',
	];

	private const collationtypes = [
		'TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT', 'VARCHAR', 'CHAR', 'ENUM', 'SET',
	];

	protected array $quotes = ['`', '`'];
	// https://dev.mysql.com/doc/refman/8.0/en/charset-unicode-sets.html
	protected string $charset = 'utf8mb4_bin'; // utf8mb4_0900_bin

	public function insert(
		string      $table,
		array       $fields,
		string|null $onConflict = null,
		string|null $conflictTarget = null,
	):array{

		$sql = match(strtoupper($onConflict ?? '')){
			'IGNORE'  => ['INSERT IGNORE'],
			'REPLACE' => ['REPLACE'],
			default   => ['INSERT'],
		};

		$sql[] = 'INTO';
		$sql[] = $this->quote($table);
		$sql[] = '('.$this->quotes[0].implode($this->quotes[1].', '.$this->quotes[0], $fields).$this->quotes[1].')';
		$sql[] = 'VALUES';
		$sql[] = '('.implode(',', array_fill(0, count($fields), '?')).')';

		return $sql;
	}

	public function createDatabase(string $dbname, bool|null $ifNotExists = null, string|null $collate = null):array{
		$collate = $collate ?? $this->charset;

		$sql = ['CREATE DATABASE'];

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

		$sql[] = 'TABLE' ;

		if($ifNotExists){
			$sql[] =  'IF NOT EXISTS' ;
		}

		$sql[] =  $this->quote($table);

		if(!empty($cols)){
			$sql[] = '(';
			$sql[] = implode(', ', $cols);

			if(!empty($primaryKey)){
				$sql[] = ', PRIMARY KEY ('.$this->quote($primaryKey).')';
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

	/**
	 * @see https://dev.mysql.com/doc/refman/8.0/en/data-types.html
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
		$type        = strtoupper(trim($type));
		$defaultType = strtoupper($defaultType ?? '');
		$field       = [$this->quote(trim($name))];

		$field[] = (is_int($length) || (is_string($length) && count(explode(',', $length)) === 2)) && !in_array($type, self::nolengthtypes, true)
			? $type.'('. $length . ')'
			: $type;

		if($attribute){
			$field[] = strtoupper($attribute);
		}

		if($collation !== null && in_array($type, self::collationtypes, true)){
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

			$field[] = match(true){
				$type === 'TIMESTAMP' && intval($defaultValue) === 0     => 'DEFAULT 0',
				$type === 'BIT'                                          => 'DEFAULT b\''.preg_replace('/[^01]/', '0', $defaultValue).'\'',
				$type === 'BOOLEAN'                                      => 'DEFAULT '.(preg_match('/^1|T|TRUE|YES$/i', $defaultValue) ? 'TRUE' : 'FALSE'),
				$type === 'BINARY' || $type === 'VARBINARY'              => 'DEFAULT 0x'.sodium_bin2hex($defaultValue),
				strtoupper($defaultValue) === 'NULL' && $isNull === true => 'DEFAULT NULL',
				default                                                  => 'DEFAULT \''.$defaultValue.'\'',
			};

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

	public function showDatabases():array{
		return ['SHOW DATABASES'];
	}

	public function showTables(string|null $database = null, string|null $pattern = null, string|null $where = null):array{
		return ['SHOW TABLES'];
	}

	public function showCreateTable(string $table):array{
		$sql = ['SHOW CREATE TABLE'];
		$sql[] = $this->quote($table);

		return $sql;
	}

}
