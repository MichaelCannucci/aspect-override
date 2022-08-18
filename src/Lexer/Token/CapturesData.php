<?php

namespace AspectOverride\Lexer\Token;

interface CapturesData
{
    /**
     * @return Capture[]
     */
    public function getCaptures(): array;
}