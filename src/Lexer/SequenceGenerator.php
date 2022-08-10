<?php

namespace AspectOverride\Lexer;

use AspectOverride\Lexer\Token\CapturesData;
use AspectOverride\Lexer\Token\Token;
use Generator;

class SequenceGenerator {

    /**
     * @var Token[]
     */
    private $tokenSequence;
    /**
     * @var OnSequenceMatched
     */
    private $onSequenceMatched;

    /** @param Token[] $tokenSequence */
    public function __construct(
        array $tokenSequence,
        OnSequenceMatched $onSequenceMatched
    ) {
        $this->tokenSequence = $tokenSequence;
        $this->onSequenceMatched = $onSequenceMatched;
    }

    /**
     * @return Generator return null if the sequence has not been matched or otherwise returns a string
     */
    public function generator(): Generator {
        while($sequenceToken = current($this->tokenSequence)) {
            $token = yield;
            $result = $sequenceToken->matches($token);
            if($result->value === SequenceResult::FAIL) {
                reset($this->tokenSequence);
                yield null;
            } else if ($result->value === SequenceResult::REUSE) {
                yield null;
            } else if ($result->value === SequenceResult::NEXT) {
                next($this->tokenSequence);
                yield $this->checkIfSequenceIsComplete($sequenceToken);
            }
        }
    }
    protected function checkIfSequenceIsComplete($sequenceToken) {
        // We got to the end of the sequence, we can consider the sequence matched
        if(false === current($this->tokenSequence)) {
            // Sequences should never end, so we restart the sequence
            reset($this->tokenSequence);

            $captures = [];
            if ($sequenceToken instanceof CapturesData) {
                $captures = $sequenceToken->getCaptures();
            }
            return ($this->onSequenceMatched)($captures);
        }
        return null;
    }
}