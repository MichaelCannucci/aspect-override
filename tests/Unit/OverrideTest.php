<?php

namespace Tests\Unit;

use AspectOverride\Facades\Registry;
use AspectOverride\Override;
use PHPUnit\Framework\TestCase;
use Tests\Util\Fixtures\OverloadedFunction;
use Tests\Util\Fixtures\MultipleMethods;
use Tests\Util\Fixtures\OneMethod;
use Tests\Util\Fixtures\TestClass;

class OverrideTest extends TestCase
{
    protected function setUp(): void
    {
        Registry::clean();
    }

    protected function tearDown(): void
    {
        Registry::clean();
    }

    public function test_can_override_class_method()
    {
        Override::method(OneMethod::class, 'say', function () {
            // Should throw an exception, but we're returning a boolean
            return true;
        });
        $this->assertTrue((new OneMethod())->say('test'));
    }

    public function test_can_override_multiple_functions()
    {
        $class = new MultipleMethods();
        foreach (['firstMethod', 'secondMethod'] as $method) {
            Override::method(MultipleMethods::class, $method, function () {
                return true;
            });
            $this->assertTrue($class->$method());
        }
    }

    public function test_can_override_global_functions()
    {
        Override::function('time', function () {
            return 10;
        });
        $class = new OverloadedFunction();
        $this->assertEquals(10, $class->time());
    }

    public function test_function_override_gets_unremoved()
    {
        Override::clean();
        $clear = Override::function('time', function () {
            return 10;
        });
        $clear();
        $this->assertNull(Registry::getForFunction('time'));
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
