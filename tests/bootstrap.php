<?php

use AspectOverride\Core\Core;

require __DIR__ . '/../vendor/autoload.php';

Core::getInstance()->init([
  'disableCaching' => true,
  'directories' => [
    __DIR__ . '/../tests'
  ]
]);