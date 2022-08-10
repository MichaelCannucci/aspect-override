<?php

namespace AspectOverride\Lexer;

interface OnSequenceMatched
{
    public function __invoke(array $captures);
}