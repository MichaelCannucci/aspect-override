<?php

use AspectOverride\Core\Instance;

require __DIR__ . '/../vendor/autoload.php';

ini_set('xdebug.var_display_max_depth', '10');
ini_set('xdebug.var_display_max_children', '256');
ini_set('xdebug.var_display_max_data', '1024');

Instance::getInstance()->init([
  'directories' => [
    __DIR__ . '/../tests'
  ]
]);