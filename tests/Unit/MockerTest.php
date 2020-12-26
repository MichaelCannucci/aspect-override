<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Tests\Util\ClassesToTestAutoload\Example1;

class MockerTest extends TestCase
{
  public function test_creates_a_mock_class()
  {
    $example = new Example1();
    $reflection = new ReflectionClass($example);
    $this->assertNotEquals('Example1.php', basename($reflection->getFileName()));
  }
}