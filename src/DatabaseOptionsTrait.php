<?php
/**
 * Trait DatabaseOptionsTrait
 *
 * @created      24.01.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\Database;

trait DatabaseOptionsTrait{

	/**
	 * The database driver to use (FQCN)
	 */
	protected ?string $driver = null;

	/**
	 * The host to connect to
	 */
	protected ?string $host = 'localhost';

	/**
	 * The port number
	 */
	protected ?int $port = null;

	/**
	 * A socket
	 */
	protected ?string $socket = null;

	/**
	 * The database name
	 */
	protected ?string $database = null;

	/**
	 * The username
	 */
	protected ?string $username = null;

	/**
	 * The password
	 */
	protected ?string $password = null;

	/**
	 * Indicates whether the connection should use SSL or not
	 */
	protected bool $use_ssl = false;

	/**
	 * The SSL key
	 */
	protected ?string $ssl_key = null;

	/**
	 * The SSL certificate
	 */
	protected ?string $ssl_cert = null;

	/**
	 * The path to a SSL certificate authority file
	 */
	protected ?string $ssl_ca = null;

	/**
	 * The directory containing SSL certificate authority files
	 */
	protected ?string $ssl_capath = null;

	/**
	 * The SSL cipher
	 */
	protected ?string $ssl_cipher = null;

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
	protected ?string $odbc_driver = null;

	/**
	 * atabase result encoding
	 *
	 * @see \mb_convert_encoding()
	 */
	protected ?string $convert_encoding_src = null;

	/**
	 * @see \mb_convert_encoding()
	 */
	protected ?string $convert_encoding_dest = 'UTF-8';

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
