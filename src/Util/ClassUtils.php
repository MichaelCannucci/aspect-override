<?php

namespace AspectOverride\Util;

class ClassUtils
{
  public static function unescapeFQN(string $classString): string
  {
    return str_replace('//', '/', $classString);
  }
  public static function escapeFQN(string $classString): string
  {
    return str_replace('/', '//', $classString);
  }
  public static function getNamespace(string $classString): string
  {
    return 
    implode('\\',
      array_slice(
        explode('\\',self::unescapeFQN($classString)),
        0,
        -1
      )
    );
  }
}