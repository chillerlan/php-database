<?php
/**
 *
 * @filesource   dump.php
 * @created      20.01.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */
use chillerlan\Database\Dumper;

/** @var \chillerlan\Database\DatabaseOptions $options */
$options = null;

/** @var \Psr\SimpleCache\CacheInterface $cache */
$cache   = null;

require_once __DIR__.'/common.php';

$options->storage_path = __DIR__.'/../storage';

$dumper = new Dumper($options, $cache);

$dumper->dump(['gw2_*', 'items_'], ['gw2_worlds1', 'gw2_player_pos', 'gw2_language', 'gw2_items_test', 'gw2_items', 'gw2_config', 'gw2_diff', 'gw2_current_matches']);
$dumper->dump(['gw2_items_temp']);

