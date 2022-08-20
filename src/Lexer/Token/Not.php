<?php

namespace AspectOverride\Lexer\Token;

use AspectOverride\Lexer\Sequence;

class Not implements TokenMatches
{
    /** @var TokenMatches */
    protected $stopToken;

    public function __construct(TokenMatches $stopToken){
        $this->stopToken = $stopToken;
    }

    public function matches(int $key, string $token, string $normalizedToken): Sequence
    {
        $result = $this->stopToken->matches($key, $token, $normalizedToken);
        if($result->value !== Sequence::FAIL) {
            return Sequence::FAIL();
        }
        return Sequence::NEXT();
    }
}