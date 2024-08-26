<?php
/**
 * Class DatabaseOptions
 *
 * @created      28.06.2017
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2017 Smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\Database;

use chillerlan\Settings\SettingsContainerAbstract;

class DatabaseOptions extends SettingsContainerAbstract{
	use DatabaseOptionsTrait;
}
