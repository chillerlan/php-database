<?php
/**
 * Class Firebird
 *
 * @filesource   Firebird.php
 * @created      11.01.2018
 * @package      chillerlan\Database\Query
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query;

class Firebird extends DialectAbstract{

	protected $quotes = ['"', '"'];

	/** @inheritdoc */
	public function select(array $from, string $where = null, $limit = null, $offset = null, bool $distinct = null, array $groupby, array $orderby):array{
		$sql[] = 'SELECT';

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
	public function createTable(string $table, array $cols, string $primaryKey = null, bool $ifNotExists, bool $temp, string $dir = null):array{
		$sql[] = $ifNotExists ? 'RECREATE' : 'CREATE'; // nasty

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

			$sql[] = '('.implode(', ', $_cols).PHP_EOL.')';
		}

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

}
