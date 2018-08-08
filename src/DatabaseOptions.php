<?php
/**
 * Class DatabaseOptions
 *
 * @filesource   DatabaseOptions.php
 * @created      28.06.2017
 * @package      chillerlan\Database
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database;

use chillerlan\Traits\ImmutableSettingsAbstract;

/**
 * @property string $driver
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
class DatabaseOptions extends ImmutableSettingsAbstract{
	use DatabaseOptionsTrait;
}
