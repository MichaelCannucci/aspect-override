<?php

namespace AspectOverride\Lexer;

interface OnSequenceMatched
{
    public function __invoke(int $start, int $end, string $code, array $captures);
}