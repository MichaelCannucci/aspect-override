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
}