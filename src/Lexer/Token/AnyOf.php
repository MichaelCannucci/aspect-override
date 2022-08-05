<?php

namespace AspectOverride\Lexer\Token;

use AspectOverride\Lexer\SequenceResult;

class AnyOf implements TokenMatches
{
    /** @var array */
    protected $tokens;
    public function __construct($tokens) { }

    public function matches(string $token): SequenceResult
    {
        return SequenceResult::fromBool(in_array($token, $this->tokens));
    }
}