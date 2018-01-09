<?php
/**
 * Class CreateDatabaseAbstract
 *
 * @filesource   CreateDatabaseAbstract.php
 * @created      28.06.2017
 * @package      chillerlan\Database\Query\Statements
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */

namespace chillerlan\Database\Query\Create;

use chillerlan\Database\Query\{
	CharsetTrait, IfNotExistsTrait, NameTrait, StatementAbstract
};

abstract class CreateDatabaseAbstract extends StatementAbstract implements CreateDatabase{
	use CharsetTrait, IfNotExistsTrait, NameTrait;
}
