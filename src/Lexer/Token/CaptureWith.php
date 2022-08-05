<?php

namespace AspectOverride\Lexer\Token;

use AspectOverride\Lexer\SequenceResult;

class CaptureWith implements TokenMatches, ProvidesData
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

    public function getData(): array {
        return $this->buffer;
    }
}