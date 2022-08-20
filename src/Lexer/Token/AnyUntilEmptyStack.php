<?php

namespace AspectOverride\Lexer\Token;

use AspectOverride\Lexer\Sequence;

class AnyUntilEmptyStack implements TokenMatches
{
    /**
     * @var int
     */
    protected $counter;
    /**
     * @var TokenMatches
     */
    protected $add;
    /**
     * @var TokenMatches
     */
    protected $remove;
    /**
     * @var int
     */
    protected $startingOffset;

    public function __construct(TokenMatches $add, TokenMatches $remove, int $startingOffset = 0) {
        $this->add = $add;
        $this->remove = $remove;
        $this->startingOffset = $startingOffset;
        $this->counter = $startingOffset;
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
                $this->counter = $this->startingOffset;
                return Sequence::NEXT();
            }
        }
        return Sequence::REUSE();
    }
}