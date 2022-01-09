<?php

namespace Tests\Util\Fixtures;

use RuntimeException;

class OneMethod
{
    public function say(string $name)
    {
        throw new RuntimeException('Should not run!');
    }
}
