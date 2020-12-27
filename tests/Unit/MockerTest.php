<?php

namespace Tests\Unit;

use AspectOverride\Override;
use PHPUnit\Framework\TestCase;
use Tests\Util\TestClasses\OverloadedFunction;
use Tests\Util\TestClasses\MultipleMethods;
use Tests\Util\TestClasses\OneMethod;
use ReflectionClass;

class MockerTest extends TestCase
{
  public function test_creates_a_mock_class()
  {
    $example = new OneMethod();
    $reflection = new ReflectionClass($example);
    $this->assertNotEquals('OneMethod.php', basename($reflection->getFileName()));
  }
  public function test_can_override_class_method()
  {
    Override::method(OneMethod::class, 'say', function(){
      // Should throw an exception, but we're returning a boolean
      return true;
    });
    $this->assertTrue((new OneMethod())->say('test'));
  }
  public function test_can_override_multiple_functions()
  {
    $class = new MultipleMethods();
    foreach (['firstMethod', 'secondMethod'] as $method) {
      Override::method(MultipleMethods::class, $method, function(){
        return true;
      });
      $this->assertTrue($class->$method());
    }
  }
  public function test_can_override_global_functions()
  {
    Override::function('time', function(){
      return 10;
    });
    $class = new OverloadedFunction();
    $this->assertEquals(10, $class->time());
  }
}