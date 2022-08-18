<?php

namespace AspectOverride\Lexer;

use AspectOverride\Lexer\Token\Capture;

class SequenceRules {

    /**
     * @var SequenceGenerator[]
     */
    private $sequences;

    /** @param SequenceGenerator[] $sequences */
    public function __construct(
        array $sequences
    ) {
        $this->sequences = $sequences;
    }

    public function transform(string $code): string {
        $buffer = [];
        $tokens = preg_split('/(\s)/', $code, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($tokens as $index => $token) {
            // Storing the token within the buffer since we want to preserve whitespace to make the code
            // very similar to before it's been transformed
            $buffer[$index] = $token;
            // skip whitespace continue or else sequence generators would fail
            if(!trim($token)) {
                continue;
            }
            // 'normalizing' a token to remove the whitespace and make it consistent to the sequence
            // (should expect all lowercase)
            $normalizedToken = trim(mb_convert_case($token, MB_CASE_LOWER));
            foreach ($this->sequences as $sequence) {
                $return = $sequence->next($index, $normalizedToken);
                if(null !== $return) {
                    /** @var Capture $capture */
                    foreach ($return as $capture) {
                        $buffer[$capture->key] = $capture->text;
                    }
                }
            }
        }
        return implode('', $buffer);
    }
}