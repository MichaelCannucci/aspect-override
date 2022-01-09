<?php

namespace Tests\Util\Fixtures;

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
