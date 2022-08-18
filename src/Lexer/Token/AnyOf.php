<?php

namespace AspectOverride\Lexer\Token;

use AspectOverride\Lexer\Sequence;

class AnyOf implements TokenMatches
{
    /** @var TokenMatches[] */
    protected $tokens;

    public function __construct(array $tokens) {
        $this->tokens = $tokens;
    }

    public function matches(int $key, string $token): Sequence
    {
        foreach ($this->tokens as $token) {
            if($token->matches($key, $token)) {
                return Sequence::fromBool(true);
            }
        }
        return Sequence::fromBool(false);
    }
}