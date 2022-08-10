<?php

namespace AspectOverride\Lexer\Token;

use AspectOverride\Lexer\SequenceResult;

class CaptureWith implements TokenMatches, CapturesData
{
    /**
     * @var TokenMatches
     */
    protected $token;

    /**
     * @var string[]
     */
    protected $buffer;

    public function __construct(TokenMatches $token)
    {
        $this->token = $token;
    }

    public function matches(string $token): SequenceResult
    {
        $sequenceResult = $this->token->matches($token);
        if($sequenceResult->value !== SequenceResult::FAIL) {
            $this->buffer[] = $token;
        }
        return $sequenceResult;
    }

    public function getCaptures(): array {
        return $this->buffer;
    }
}