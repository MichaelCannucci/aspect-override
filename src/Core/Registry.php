<?php

namespace AspectOverride\Core;

class Registry
{
  /** @var array<string,array<string,callable> */
  protected $classMap = [];
  /** @var array<string,callable> */
  protected $fnMap = [];

  /** @param class-string $class */
  public static function setForClass(string $class, string $method, callable $fn): void
  {
    self::$classMap[self::escapeClass($class)][$method] = $fn;
  }
  /** @param class-string $class */
  public static function getForClass(string $class, string $method): ?callable
  {
    return self::$classMap[self::escapeClass($class)][$method] ?? null;
  }
  public static function setForFunction(string $fnName, callable $fn): void
  {
    self::$fnMap[$fnName] = $fn;
  }
  public static function getForFunction(string $fn): ?callable
  {
    return self::$fnMap[$fn] ?? null;
  }
  protected static function escapeClass(string $classString)
  {
    return str_replace('//', '/', $classString);
  }
}