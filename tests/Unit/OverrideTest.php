<?php

namespace Tests\Unit;

use AspectOverride\Override;
use PHPUnit\Framework\TestCase;
use Tests\Util\Fixtures\TestClass;

class OverrideTest extends TestCase
{
    protected function setUp(): void
    {
        Override::reset();
    }

    protected function tearDown(): void
    {
        Override::reset();
    }

    public function test_function_has_void_return()
    {
        Override::method(TestClass::class, 'voidReturn', function () {
            // Anything so the method doesn't throw
        });
        $class = new TestClass();
        $this->assertNull($class->voidReturn());
    }

    public function test_private_function()
    {
        Override::method(TestClass::class, 'privateMethod', function () {
            return null;
        });
        $class = new TestClass();
        $this->assertNull($class->fromPrivateMethod());
    }

    public function test_protected_function()
    {
        Override::method(TestClass::class, 'protectedMethod', function () {
            return null;
        });
        $class = new TestClass();
        $this->assertNull($class->fromProtectedMethod());
    }
}
