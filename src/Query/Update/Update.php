<?php
/**
 * Interface Update
 *
 * @filesource   Update.php
 * @created      03.06.2017
 * @package      chillerlan\Database\Query\Update
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Update;

use chillerlan\Database\Query\Statement;

/**
 * @link https://www.sqlite.org/lang_update.html
 * @link https://dev.mysql.com/doc/refman/5.7/en/update.html
 * @link https://www.postgresql.org/docs/current/static/sql-update.html
 * @link https://msdn.microsoft.com/library/ms177523(v=sql.110).aspx
 * @link https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25-dml-update.html
 */
interface Update extends Statement{

	/**
	 * @param string $tablename
	 *
	 * @return \chillerlan\Database\Query\Update\Update
	 */
	public function table(string $tablename);

	/**
	 * @param array $set
	 * @param bool  $bind
	 *
	 * @return \chillerlan\Database\Query\Update\Update
	 */
	public function set(array $set, bool $bind = null):Update;

}
