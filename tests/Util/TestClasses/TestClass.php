<?php

namespace Tests\Util\TestClasses;

use PhpParser\Node\Expr\FuncCall;

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
}