<?php

use AspectOverride\Override;
use PHPUnit\Framework\TestCase;
use Tests\Util\Fixtures\BadlyFormattedClass;

class BadlyFormattedClassesTest extends TestCase
{
    public function test_badly_fomatted_methods()
    {
        $reflection = new ReflectionClass(BadlyFormattedClass::class);
        $class = new BadlyFormattedClass();
        foreach ($reflection->getMethods() as $method) {
            Override::method(BadlyFormattedClass::class, $method->getName(), function () {
                return true;
            });
            $this->assertTrue($class->{$method->getName()}(), "Method: '{$method->getName()}' is not stubbed");
        }
    }
}
