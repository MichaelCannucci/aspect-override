<?php

namespace Tests\Unit;

use AspectOverride\Override;
use PHPUnit\Framework\TestCase;
use Tests\Util\Fixtures\AbstractClass;
use Tests\Util\Fixtures\AbstractClassImplementation;
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
        $this->assertNull((new TestClass)->voidReturn());
    }

    public function test_private_function()
    {
        Override::method(TestClass::class, 'privateMethod', function () {
            return null;
        });
        $this->assertNull((new TestClass())->fromPrivateMethod());
    }

    public function test_protected_function()
    {
        Override::method(TestClass::class, 'protectedMethod', function () {
            return null;
        });
        $this->assertNull((new TestClass())->fromProtectedMethod());
    }

    public function test_static_function() {
        Override::method(TestClass::class, 'staticFunction', function() {
            return null;
        });
        $this->assertNull(TestClass::staticFunction());
    }

    public function test_no_white_space_function() 
    {
        Override::method(TestClass::class, 'noWhiteSpace', function() {
            return 1;
        });
        $this->assertEquals(1, (new TestClass)->noWhiteSpace());
    }

    public function test_empty_function()
    {
        Override::method(TestClass::class, 'emptyFunction', function() {
            return 1;
        });
        $this->assertEquals(1, (new TestClass)->emptyFunction());
    }

    public function test_abstract_implemented_function() {
        Override::method(AbstractClass::class, 'B', function() {
            return 1;
        });
        $this->assertEquals(1, (new AbstractClassImplementation())->B());
    }
}
