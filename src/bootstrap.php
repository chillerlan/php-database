<?php
/**
 * Database bootstrapper
 *
 * @filesource   bootstrap.php
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

/**
 * prevent notices in case sqlite3 extension is missing
 */

if(!defined('SQLITE3_OPEN_READWRITE')){
	define('SQLITE3_OPEN_READWRITE', 2);
}

if(!defined('SQLITE3_OPEN_CREATE')){
	define('SQLITE3_OPEN_CREATE', 4);
}

