<?php

namespace Tests\Util\Fixtures;

class TestClass
{
    public function voidReturn(): void
    {
        throw new \RuntimeException("Should not run!");
    }

    public function scalarReturnType(): int
    {
        throw new \RuntimeException("Should not run!");
    }

    private function privateMethod()
    {
        throw new \RuntimeException("Should not run!");
    }

    protected function protectedMethod()
    {
        throw new \RuntimeException("Should not run!");
    }

    public function fromPrivateMethod()
    {
        return $this->privateMethod();
    }

    public function fromProtectedMethod()
    {
        return $this->protectedMethod();
    }

    public function emptyFunction()
    {
    }

    public function noWhiteSpace(){}

    public static function staticFunction()
    {
        throw new \RuntimeException("Should not run!");
    }
}
