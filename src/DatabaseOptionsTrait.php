<?php
/**
 * Trait DatabaseOptionsTrait
 *
 * @created      24.01.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database;

/**
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
 * @property string $odbc_driver
 * @property string $convert_encoding_src
 * @property string $convert_encoding_dest
 * @property int    $mssql_timeout
 * @property string $mssql_charset
 * @property bool   $mssql_encrypt
 * @property string $firebird_encoding
 * @property string $cachekey_hash_algo
 * @property string $storage_path
 */
trait DatabaseOptionsTrait{

	/**
	 * The host to connect to
	 */
	protected string|null $host = 'localhost';

	/**
	 * The port number
	 */
	protected int|null $port = null;

	/**
	 * A socket
	 */
	protected string|null $socket = null;

	/**
	 * The database name
	 */
	protected string|null $database = null;

	/**
	 * The username
	 */
	protected string|null $username = null;

	/**
	 * The password
	 */
	protected string|null $password = null;

	/**
	 * Indicates whether the connection should use SSL
	 */
	protected bool $use_ssl = false;

	/**
	 * The SSL key
	 */
	protected string|null $ssl_key = null;

	/**
	 * The SSL certificate
	 */
	protected string|null $ssl_cert = null;

	/**
	 * The path to a SSL certificate authority file
	 */
	protected string|null $ssl_ca = null;

	/**
	 * The directory containing SSL certificate authority files
	 */
	protected string|null $ssl_capath = null;

	/**
	 * The SSL cipher
	 */
	protected string|null $ssl_cipher = null;

	/**
	 * MySQLi connection timeout
	 */
	protected int $mysqli_timeout = 3;

	/**
	 * MySQL connection character set
	 *
	 * @link https://mathiasbynens.be/notes/mysql-utf8mb4 How to support full Unicode in MySQL
	 */
	protected string $mysql_charset = 'utf8mb4';

	/**
	 * PostgreSQL connection character set
	 */
	protected string $pgsql_charset = 'UTF8';

	/**
	 * The driver name to use for an ODBC connection (@todo)
	 */
	protected string|null $odbc_driver = null;

	/**
	 * Database result encoding
	 *
	 * @see \mb_convert_encoding()
	 */
	protected string|null $convert_encoding_src = null;

	/**
	 * @see \mb_convert_encoding()
	 */
	protected string|null $convert_encoding_dest = 'UTF-8';

	/**
	 * MS SQL Server connection timeout
	 *
	 * @link https://docs.microsoft.com/en-us/sql/connect/php/connection-options
	 */
	protected int $mssql_timeout = 3;

	/**
	 * MS SQL Server connection character set
	 */
	protected string $mssql_charset = 'UTF-8';

	/**
	 * Specifies whether the communication with MS SQL Server is encrypted or unencrypted.
	 */
	protected int $mssql_encrypt = 0; // @todo: how???

	/**
	 * Firebird connection encoding
	 */
	protected string $firebird_encoding = 'UTF8';

	/**
	 * a hash algorithm for the cache keys
	 */
	protected string $cachekey_hash_algo = 'sha256';

	/**
	 * @todo
	 */
	protected string $storage_path;

}
