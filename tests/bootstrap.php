<?php

require __DIR__ . '/../vendor/autoload.php';

AspectOverride\Facades\Instance::initialize(
    AspectOverride\Core\Configuration::create()
        ->setDirectories([
            __DIR__ . '/../tests/Util/Fixtures'
        ])
);
