<?php

namespace AspectOverride\Lexer\Token;

use AspectOverride\Lexer\Sequence;

class AnyUntil implements TokenMatches
{
    /** @var TokenMatches */
    protected $until;

    public function __construct(TokenMatches $until){
        $this->until = $until;
    }

    public function matches(int $key, string $token, string $normalizedToken): Sequence
    {
        $result = $this->until->matches($key, $token, $normalizedToken);
        if($result->value === Sequence::FAIL) {
            return Sequence::REUSE();
        }
        return Sequence::NEXT();
    }
}