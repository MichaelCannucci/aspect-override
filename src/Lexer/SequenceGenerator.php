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

    public function next(int $index, string $token, string $normalizedToken): ?array {
        $sequenceToken = current($this->tokenSequence);
        $result = $sequenceToken->matches($index, $token, $normalizedToken);
        if($result === Sequence::FAIL()) {
            reset($this->tokenSequence);
            return null;
        } else if ($result === Sequence::REUSE()) {
            return null;
        } else if ($result === Sequence::NEXT()) {
            next($this->tokenSequence);
            return $this->checkIfSequenceIsComplete();
        }
        return $this->checkIfSequenceIsComplete();
    }

    protected function checkIfSequenceIsComplete() {
        // We got to the end of the sequence, we can consider the sequence matched
        if(false === current($this->tokenSequence)) {
            // Sequences should never end, so we restart the sequence
            reset($this->tokenSequence);

            $captures = [];
            foreach ($this->tokenSequence as $sequence) {
                if ($sequence instanceof CapturesData) {
                    foreach ($sequence->popCaptures() as $capture) {
                        $captures[] = $capture;
                    }
                }
            }
            return ($this->onSequenceMatched)($captures);
        }
        return null;
    }
}