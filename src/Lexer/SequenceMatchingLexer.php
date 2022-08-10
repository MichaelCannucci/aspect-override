<?php

namespace AspectOverride\Lexer;

class SequenceMatchingLexer {

    protected const OFFSET_INDEX = 1;
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
        $sequenceGenerators = array_map(function(SequenceGenerator $sequence) {
            return $sequence->generator();
        }, $this->sequences);
        $tokens = preg_split('/(\s)/', $code, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($tokens as $token) {
            // Storing the token within the buffer since we want to preserve whitespace to make the code
            // very similar to before it's been transformed
            $buffer[] = $token;
            // skip whitespace continue or else sequence generators would fail
            if(!trim($token)) {
                continue;
            }
            // 'normalizing' a token to remove the whitespace and make it consistent to the sequence
            // (should expect all lowercase)
            $normalizedToken = trim(mb_convert_case($token, MB_CASE_LOWER));
            foreach ($sequenceGenerators as $generator) {
                $generator->send($normalizedToken);
                $return = $generator->current();
                if($return) {
                    $buffer[] = $return;
                }
            }
        }
        return implode('', $buffer);
    }
}