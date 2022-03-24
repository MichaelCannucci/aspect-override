<?php

namespace Tests\Util\Fixtures;

class AbstractClassImplementation extends AbstractClass
{
    function test(): void {
        throw new \RuntimeException("Should not run!");
    }
    function testA(): int {
        throw new \RuntimeException("Should not run!");
    }
}