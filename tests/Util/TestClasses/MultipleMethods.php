<?php

namespace Tests\Util\TestClasses;

class MultipleMethods
{
  public function firstMethod()
  {
    throw new \RuntimeException("Should not run!");
  }
  public function secondMethod()
  {
    throw new \RuntimeException("Should not run!");
  }
}