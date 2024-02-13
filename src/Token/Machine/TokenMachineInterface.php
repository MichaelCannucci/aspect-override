<?php

namespace AspectOverride\Token\Machine;

interface TokenMachineInterface
{

    public function process(\PhpToken $token, ?\PhpToken $before = null): string;

    public function reset(): void;
}