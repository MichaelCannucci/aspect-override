<?php

namespace AspectOverride\Lexer\Token;

use AspectOverride\Lexer\Sequence;

class Regex implements TokenMatches
{
    /**
     * @var string
     */
    protected $pattern;

    public function __construct(string $pattern){
        $this->pattern = $pattern;
    }

    public function matches(int $key, string $token, string $normalizedToken): Sequence
    {
        return preg_match($this->pattern, $normalizedToken) ? Sequence::NEXT() : Sequence::FAIL();
    }
}