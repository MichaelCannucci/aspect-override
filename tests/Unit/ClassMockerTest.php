<?php

namespace Tests\Unit;

use AspectOverride\Mocking\ClassMocker;
use PHPUnit\Framework\TestCase;

/**
 * Note: Actual loading and mocking is tested implicitly by "MockerTest.php"
 */
class ClassMockerTest extends TestCase
{
  public function test_can_create_new_with_defaults()
  {
    $this->assertInstanceOf(ClassMocker::class, new ClassMocker());
  }
}