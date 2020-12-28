<?php

namespace Tests\Unit;

use AspectOverride\Core\Registry;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use TRegx\DataProvider\CrossDataProviders;

class RegistryTest extends TestCase
{
  protected function setUp(): void
  {
    Registry::clean();
  }
  protected function tearDown(): void
  {
    Registry::clean();
  }
  /**
   * @dataProvider class_provider
   */
  public function test_can_save_callback(string $class, string $method)
  {
    Registry::setForClass($class, $method, function(){});
    $this->assertNotNull(Registry::getForClass($class, $method));
  }
  /**
   * @dataProvider class_provider
   */
  public function test_can_remove_callback(string $class, string $method)
  {
    Registry::setForClass($class, $method, function(){});
    Registry::removeForClass($class, $method);
    $this->assertNull(Registry::getForClass($class, $method));
  }
  public function class_provider()
  {
    return CrossDataProviders::cross(
      [
        ['Test'],
        ['Test\A\B'],
        ['Test\A\B\A']
      ],
      [
        ['test']
      ]
    );
  }
}