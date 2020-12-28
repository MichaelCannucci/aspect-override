<?php

namespace Tests\Unit;

use AspectOverride\Util\ClassUtils;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use TRegx\DataProvider\CrossDataProviders;

class ClassUtilsTest extends TestCase
{
  /**
   * @dataProvider namespace_provider
   */
  public function test_get_namespace_from_class($class, $expected)
  {
    $actual = ClassUtils::getNamespace($class);
    $this->assertEquals($expected, $actual);
  }
  public function namespace_provider()
  {
    return [
      ['Test',           ''],
      ['Test\A',         'Test'],
      ['Test\A\B',       'Test\A'],
      ['Test\\\\A\\\\B', 'Test\A'],
      ['Test\\\\A',      'Test'],
    ];
  }
}