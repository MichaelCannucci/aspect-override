<?php

namespace AspectOverride\Lexer\Traits;

use AspectOverride\Lexer\Token\Token;

/**
 * Bad enum replacement for 7.1
 */
trait Constants
{
    public static function __callStatic($name, $arguments)
    {
        static $options;
        if(!$options) {
            $options = (new \ReflectionClass(Token::class))->getConstants();
        }
        if(array_key_exists($name, $options)) {
            return new self($name);
        }
        throw new \InvalidArgumentException("No predefined token named: $name");
    }

}