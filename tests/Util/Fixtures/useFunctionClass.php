<?php

namespace Tests\Util\Fixtures;

use function explode;

class useFunctionClass
{
    public function test() {
        throw new \RuntimeException("Should not run!");
    }
}