<?php

namespace AspectOverride\Lexer;

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
        OnSequenceMatched $onSequenceMatched,
    ) {
        $this->tokenSequence = $tokenSequence;
        $this->onSequenceMatched = $onSequenceMatched;
    }

    public function start(string &$code): Generator {
        while($sequence = current($this->tokenSequence)) {
            [$token, $start, $end] = yield;
            $result = $sequence->matches($token);
            if($result->value === SequenceResult::FAIL) {
                reset($this->tokenSequence);
                yield false;
            } else if ($result->value === SequenceResult::REUSE) {
                yield true;
            } else if ($result->value === SequenceResult::NEXT) {
                next($this->tokenSequence);
                yield true;
            }
            // We got to the end of the sequence, we can consider the sequence matched
            if(false === current($this->tokenSequence)) {
                ($this->onSequenceMatched)($start, $end, $code);
            }
            // Reset to let the sequence continue
            reset($this->tokenSequence);
        }
    }
}