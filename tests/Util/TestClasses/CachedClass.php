<?php

namespace Tests\Util\TestClasses;

class CachedClass
{
    public function run()
    {
        throw new \RuntimeException("Should not run!");
    }
}