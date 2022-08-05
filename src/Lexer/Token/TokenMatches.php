<?php

namespace AspectOverride\Lexer\Token;

use AspectOverride\Lexer\SequenceResult;

interface TokenMatches
{
    public function matches(string $token): SequenceResult;
}