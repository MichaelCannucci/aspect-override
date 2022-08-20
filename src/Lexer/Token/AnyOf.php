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

    public function matches(int $key, string $token, string $normalizedToken): Sequence
    {
        foreach ($this->tokens as $tokens) {
            $result = $tokens->matches($key, $token, $normalizedToken);
            if($result !== Sequence::FAIL()) {
                return $result;
            }
        }
        return Sequence::FAIL();
    }
}