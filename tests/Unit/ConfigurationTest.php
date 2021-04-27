<?php

namespace Tests\Unit;

use AspectOverride\Core\Configuration;
use AspectOverride\Facades\Instance;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function testConfigurationBuiltCorrectly()
    {
        $previous = Instance::getConfiguration();
        Instance::initialize(
            Configuration::create()
            ->setTemporaryFilesDirectory('/tmp/test/')
            ->setDirectories([ //__DIR__ should remain and the other directory should be removed (due to realpath), since it's invalid
                __DIR__,
                'test2'
            ])
        );
        self::assertEquals([__DIR__], Instance::getConfiguration()->getDirectories());
        self::assertEquals('/tmp/test/', Instance::getConfiguration()->getTemporaryFilesDirectory());
        // Restore
        Instance::initialize($previous);
    }
}