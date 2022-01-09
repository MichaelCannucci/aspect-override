<?php

require __DIR__ . '/../vendor/autoload.php';

AspectOverride\Builder::create()
    ->setAllowedDirectories([
        __DIR__ . '/../tests/Util/Fixtures'
    ])
    ->load();
