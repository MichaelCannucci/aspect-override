<?php

namespace AspectOverride\Lexer\Token;

use AspectOverride\Lexer\Sequence;

interface TokenMatches
{
    public function matches(int $key, string $token, string $normalizedToken): Sequence;
}