<?php

namespace AspectOverride\Token\Machine;

interface TokenMachineInterface
{

    public function process(\PhpToken $token): string;

    public function reset(): void;
}