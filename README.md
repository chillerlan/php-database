# chillerlan/php-database

A PHP SQL client and querybuilder for the most common databases, namely: MySQL, PostgreSQL, SQLite3, Microsoft SQL Server (Transact) and Firebird.

[![PHP Version Support][php-badge]][php]
[![version][packagist-badge]][packagist]
[![license][license-badge]][license]
[![Continuous Integration][gh-action-badge]][gh-action]
[![Coverage][coverage-badge]][coverage]
[![Packagist downloads][downloads-badge]][downloads]

[php-badge]: https://img.shields.io/packagist/php-v/chillerlan/php-database?logo=php&color=8892BF&logoColor=fff
[php]: https://www.php.net/supported-versions.php
[packagist-badge]: https://img.shields.io/packagist/v/chillerlan/php-database.svg?logo=packagist&logoColor=fff
[packagist]: https://packagist.org/packages/chillerlan/php-database
[license-badge]: https://img.shields.io/github/license/chillerlan/php-database.svg
[license]: https://github.com/chillerlan/php-database/blob/main/LICENSE
[gh-action-badge]: https://img.shields.io/github/actions/workflow/status/chillerlan/php-database/ci.yml?branch=main&logo=github&logoColor=fff
[gh-action]: https://github.com/chillerlan/php-database/actions/workflows/ci.yml?query=branch%3Amain
[coverage-badge]: https://img.shields.io/codecov/c/github/chillerlan/php-database.svg?logo=codecov&logoColor=fff
[coverage]: https://codecov.io/github/chillerlan/php-database
[downloads-badge]: https://img.shields.io/packagist/dt/chillerlan/php-database.svg?logo=packagist&logoColor=fff
[downloads]: https://packagist.org/packages/chillerlan/php-database/stats

# Documentation

## Requirements
- PHP 8.2+
- one of the supported databases, set up to work with PHP:
  - [MySQL](https://dev.mysql.com/doc/refman/5.6/en/) (5.5+) / [MariaDB](https://mariadb.com/kb/en/library/basic-sql-statements/) via [ext-mysqli](https://www.php.net/manual/en/book.mysqli.php) or [ext-pdo_mysql](https://www.php.net/manual/en/ref.pdo-mysql.php)
  - [PostgreSQL](https://www.postgresql.org/docs/9.5/static/index.html) (9.5+) via [ext-pgsql](https://www.php.net/manual/en/book.pgsql.php) or [ext-pdo_pgsql](https://www.php.net/manual/en/ref.pdo-pgsql.php)
  - [SQLite3](https://www.sqlite.org/lang.html) via [ext-pdo_sqlite](https://www.php.net/manual/en/ref.pdo-sqlite.php)
  - [Firebird](https://www.firebirdsql.org/file/documentation/reference_manuals/fblangref25-en/html/fblangref25.html) (2.5+) via [ext-pdo_firebird](https://www.php.net/manual/en/ref.pdo-firebird.php)
  - [Microsoft SQL Server](https://www.microsoft.com/en-us/sql-server/sql-server-downloads) ([transact-sql](https://docs.microsoft.com/sql/t-sql/language-reference)) via [ext-sqlsrv or ext-pdo_sqlsrv](https://github.com/Microsoft/msphpsql)

## Installation
**requires [composer](https://getcomposer.org)**

### *composer.json*
(note: replace `dev-main` with a [version boundary](https://getcomposer.org/doc/articles/versions.md#summary))
```json
{
	"require": {
		"php": "^8.2",
		"chillerlan/php-database": "dev-main"
	}
}
```

Profit!

