<?php

namespace AspectOverride\Lexer;

use AspectOverride\Lexer\Token\CapturesData;
use AspectOverride\Lexer\Token\Token;

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

    public function next(int $index, string $token): ?array {
        $sequenceToken = current($this->tokenSequence);
        $result = $sequenceToken->matches($index, $token);
        if($result->value === Sequence::FAIL) {
            reset($this->tokenSequence);
            return null;
        } else if ($result->value === Sequence::REUSE) {
            return null;
        } else if ($result->value === Sequence::NEXT) {
            next($this->tokenSequence);
            return $this->checkIfSequenceIsComplete($sequenceToken);
        }
        return $this->checkIfSequenceIsComplete($sequenceToken);
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