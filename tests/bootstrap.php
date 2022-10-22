<?php

use AspectOverride\Core\Configuration;
use AspectOverride\Facades\AspectOverride;

AspectOverride::initialize(
    Configuration::create()
        ->setDirectories([__DIR__ . '/../tmp/code'])
        ->setDebugDump(__DIR__ . '/../tmp/debug')
);
