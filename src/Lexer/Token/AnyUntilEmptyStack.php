<?php

namespace AspectOverride\Lexer\Token;

use AspectOverride\Lexer\Sequence;

class AnyUntilEmptyStack implements TokenMatches
{
    protected $counter;
    /**
     * @var TokenMatches
     */
    protected $add;
    /**
     * @var TokenMatches
     */
    protected $remove;

    public function __construct(TokenMatches $add, TokenMatches $remove) {
        $this->add = $add;
        $this->remove = $remove;
    }

    public function matches(int $key, string $token, string $normalizedToken): Sequence
    {
        $add = $this->add->matches($key, $token, $normalizedToken);
        $remove = $this->remove->matches($key, $token, $normalizedToken);
        if($add->value !== Sequence::FAIL) {
            $this->counter = $this->counter + 1;
        }
        if($remove->value !== Sequence::FAIL) {
            $this->counter = $this->counter - 1;
            if($this->counter === 0) {
                return Sequence::NEXT();
            }
        }
        return Sequence::REUSE();
    }
}