<?php
/**
 * Class Options
 *
 * @filesource   Options.php
 * @created      28.06.2017
 * @package      chillerlan\Database
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database;

use chillerlan\Database\Traits\Container;

/**
 * @property string $driver
 * @property string $querybuilder
 * @property string $host
 * @property int    $port
 * @property string $socket
 * @property string $database
 * @property string $username
 * @property string $password
 * @property bool   $use_ssl
 * @property string $ssl_key
 * @property string $ssl_cert
 * @property string $ssl_ca
 * @property string $ssl_capath
 * @property string $ssl_cipher
 * @property int    $mysqli_timeout
 * @property string $mysql_charset
 * @property string $pgsql_charset
 * @property string $sqlite_flags
 * @property string $sqlite_encryption_key
 * @property string $odbc_driver
 * @property string $convert_encoding_src
 * @property string $convert_encoding_dest
 * @property int    $mssql_timeout
 * @property string $mssql_charset
 * @property bool   $mssql_encrypt
 */
class Options{
	use Container;

	/**
	 * The database driver to use (FQCN)
	 *
	 * @var string
	 */
	protected $driver;

	/**
	 * The query builder to use (FQCN) [optional]
	 *
	 * @var string
	 */
	protected $querybuilder;

	/**
	 * The host to connect to
	 *
	 * @var string
	 */
	protected $host = 'localhost';

	/**
	 * The port number
	 *
	 * @var int
	 */
	protected $port;

	/**
	 * A socket
	 *
	 * @var string
	 */
	protected $socket;

	/**
	 * The database name
	 *
	 * @var string
	 */
	protected $database;

	/**
	 * The username
	 *
	 * @var string
	 */
	protected $username;

	/**
	 * The password
	 *
	 * @var string
	 */
	protected $password;

	/**
	 * Indicates whether the connection should use SSL or not
	 *
	 * @var bool
	 */
	protected $use_ssl = false;

	/**
	 * The SSL key
	 *
	 * @var string
	 */
	protected $ssl_key;

	/**
	 * The SSL certificate
	 *
	 * @var string
	 */
	protected $ssl_cert;

	/**
	 * The path to a SSL certificate authority file
	 *
	 * @var string
	 */
	protected $ssl_ca;

	/**
	 * The directory containing SSL certificate authority files
	 *
	 * @var string
	 */
	protected $ssl_capath;

	/**
	 * The SSL cipher
	 *
	 * @var string
	 */
	protected $ssl_cipher;

	/**
	 * MySQLi connection timeout
	 *
	 * @var int
	 */
	protected $mysqli_timeout = 1;

	/**
	 * MySQL connection character set
	 *
	 * @link https://mathiasbynens.be/notes/mysql-utf8mb4 How to support full Unicode in MySQL
	 *
	 * @var string
	 */
	protected $mysql_charset = 'utf8mb4';

	/**
	 * PostgreSQL connection character set
	 *
	 * @var string
	 */
	protected $pgsql_charset = 'UTF8';

	/**
	 * The driver name to use for an ODBC connection
	 *
	 * @var string
	 */
	protected $odbc_driver;

	/**
	 * @var string database result encoding (mb_convert_encoding)
	 */
	protected $convert_encoding_src;

	/**
	 * @var string target encoding
	 */
	protected $convert_encoding_dest = 'UTF-8';

	/**
	 * MS SQL Server connection timeout
	 *
	 * @link https://docs.microsoft.com/en-us/sql/connect/php/connection-options
	 *
	 * @var int
	 */
	protected $mssql_timeout = 1;

	/**
	 * MS SQL Server connection character set
	 *
	 * @var string
	 */
	protected $mssql_charset = 'UTF-8';

	/**
	 * Specifies whether the communication with MS SQL Server is encrypted or unencrypted.
	 *
	 * @var bool
	 */
	protected $mssql_encrypt = false; // how???

}
