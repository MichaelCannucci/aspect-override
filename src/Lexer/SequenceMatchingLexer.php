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
        preg_match_all('/\s/', $code, $matches, PREG_OFFSET_CAPTURE);
        $lastIndex = 0;
        $validSequences = array_map(function(SequenceGenerator $seq) {
            return $seq->start($code);
        }, $this->sequences);
        foreach (($matches[0] ?? []) as $match) {
            $offset = $match[self::OFFSET_INDEX];
            $token = mb_convert_case(substr($code, $lastIndex, $offset), MB_CASE_LOWER);
            foreach ($validSequences as $key => $sequence) {
                $sequence->send([$token, $lastIndex, $offset]);
                if(false === $sequence->current()) {
                    unset($validSequences[$key]);
                }
            }
            $lastIndex = $offset;
        }
    }
}