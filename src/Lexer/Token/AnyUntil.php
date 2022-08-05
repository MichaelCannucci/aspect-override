<?php

namespace AspectOverride\Lexer\Token;

use AspectOverride\Lexer\SequenceResult;

class AnyUntil implements TokenMatches
{
    /** @var TokenMatches */
    protected $until;

    public function __construct(TokenMatches $until){
    }

    public function matches(string $token): SequenceResult
    {
        $result = $this->until->matches($token);
        if($result->value === SequenceResult::FAIL) {
            return SequenceResult::REUSE();
        }
        return SequenceResult::NEXT();
    }
}