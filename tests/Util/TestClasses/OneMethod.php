<?php

namespace Tests\Util\TestClasses;

use RuntimeException;

class OneMethod
{
    public function say(string $name)
    {
        throw new RuntimeException('Should not run!');
    }
}