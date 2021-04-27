<?php

require __DIR__ . '/../vendor/autoload.php';

ini_set('xdebug.var_display_max_depth', '10');
ini_set('xdebug.var_display_max_children', '256');
ini_set('xdebug.var_display_max_data', '1024');

AspectOverride\Facades\Instance::initialize(
    \AspectOverride\Core\Configuration::create()
        ->setDirectories([
            __DIR__ . '/../tests/Util/TestClasses'
        ])
);