<?php

namespace AspectOverride\Lexer;

interface OnSequenceMatched
{
    /** @param array{0:int,1:string}[] $captures */
    public function __invoke(array $captures): array;
}