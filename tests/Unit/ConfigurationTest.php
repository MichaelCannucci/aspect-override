<?php

namespace tests\Unit;

use AspectOverride\Core\Instance;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
  public function testConfigurationBuiltCorrectly()
  {
    $previous = Instance::getInstance()->getConfiguration()->getRaw();
    Instance::getInstance()->init([
      'temporaryFilesDir' => '/tmp/test/',
      'directories' => [__DIR__,'test2'] //__DIR__ should remain and the other directory should be removed (due to realpath), since it's invalid
    ]);
    $this->assertEquals(Instance::getInstance()->getDirectories(), [__DIR__]);
    $this->assertEquals(Instance::getInstance()->getTemporaryDirectory(), '/tmp/test/');
    // Restore
    Instance::getInstance()->init($previous);
  }
}