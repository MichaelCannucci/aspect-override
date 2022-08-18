<?php

namespace AspectOverride\Lexer\Token;

class Capture
{
    /**
     * @readonly
     * @var int
     */
    public $key;
    /**
     * @var string
     */
    public $text;

    public function __construct(int $key, string $text) {

        $this->key = $key;
        $this->text = $text;
    }
}