<?php

use AspectOverride\Core\Core;

require __DIR__ . '/../vendor/autoload.php';

// Remove!
ini_set('xdebug.var_display_max_depth', '10');
ini_set('xdebug.var_display_max_children', '256');
ini_set('xdebug.var_display_max_data', '1024');

Core::getInstance()->init([
  'disableCaching' => true,
  'directories' => [
    __DIR__ . '/../tests'
  ]
]);