<?php

namespace AspectOverride\Lexer\Token;

use AspectOverride\Lexer\Sequence;

class AndToken implements TokenMatches
{
    /** @var TokenMatches */
    protected $first;
    /** @var TokenMatches */
    protected $second;

    public function __construct(TokenMatches $first, TokenMatches $second){
        $this->first = $first;
        $this->second = $second;
    }

    public function matches(int $key, string $token, string $normalizedToken): Sequence
    {
        $first = $this->first->matches($key, $token, $normalizedToken);
        $second = $this->second->matches($key, $token, $normalizedToken);
        if($first !== Sequence::FAIL() && $second !== Sequence::FAIL()) {
            // Quirk, return the first sequence arbitrarily
            return $first;
        }
        return Sequence::FAIL();
    }
}