<?php

namespace Tests\Util\ClassesToTestAutoload;

use RuntimeException;

class Example1
{
  public function say(string $name)
  {
    throw new RuntimeException('Class should be have been mocked!');
  }
}