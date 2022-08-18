<?php

namespace AspectOverride\Lexer\Token;

use AspectOverride\Lexer\Sequence;

class CaptureWith implements TokenMatches, CapturesData
{
    /**
     * @var TokenMatches
     */
    protected $token;

    /**
     * @var Capture[]
     */
    protected $captures;

    public function __construct(TokenMatches $token)
    {
        $this->token = $token;
    }

    public function matches(int $key, string $token): Sequence
    {
        $sequenceResult = $this->token->matches($key, $token);
        if($sequenceResult->value !== Sequence::FAIL) {
            $this->captures[] = new Capture($key, $token);
        }
        return $sequenceResult;
    }

    public function getCaptures(): array {
        return $this->captures;
    }
}