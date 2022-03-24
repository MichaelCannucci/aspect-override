<?php

namespace Tests\Util\Fixtures;

abstract class AbstractClass
{
    abstract function test(): void;

    abstract function testA(): int;

    function B() {
        throw new \RuntimeException("Should not run!");
    }

    private function C() {
        throw new \RuntimeException("Should not run!");
    }
}