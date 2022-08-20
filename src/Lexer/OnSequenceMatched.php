<?php

namespace AspectOverride\Lexer;

use AspectOverride\Lexer\Token\Capture;

interface OnSequenceMatched
{
    /** @param Capture[] $captures */
    public function __invoke(array $captures): array;
}