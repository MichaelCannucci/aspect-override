<?php

namespace AspectOverride\Lexer\Traits;

use AspectOverride\Lexer\Token\Token;

/**
 * Bad enum replacement for 7.1
 */
trait ConstantContainer
{
    public static function __callStatic($name, $arguments)
    {
        static $options = [];
        static $cache = [];
        $class = get_class();
        if(!$options) {
            $options = (new \ReflectionClass($class))->getConstants();
        }
        if(array_key_exists($name, $options)) {
            $option = $options[$name];
            if(!array_key_exists($class, $cache) || !array_key_exists($option, $cache[$class])) {
                $cache[$class][$option] = new self($option);
            }
            return $cache[$class][$option];
        }
        throw new \InvalidArgumentException("No predefined constant named: $name");
    }

}