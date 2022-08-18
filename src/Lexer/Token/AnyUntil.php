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

    public function matches(int $key, string $token): Sequence
    {
        $result = $this->until->matches($key, $token);
        if($result->value === Sequence::FAIL) {
            return Sequence::REUSE();
        }
        return Sequence::NEXT();
    }
}