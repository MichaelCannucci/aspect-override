<?php

namespace AspectOverride\Core;

use AspectOverride\Util\ClassUtils;

class Registry
{
  /** @var array<string,array<string,callable>> */
  protected static $classMap = [];
  /** @var array<string,callable> */
  protected static $fnMap = [];

  /** @param class-string $class */
  public static function setForClass(string $class, string $method, callable $fn): void
  {
    self::$classMap[$class][$method] = $fn;
  }
  /** @param class-string $class */
  public static function getForClass(string $class, string $method): ?callable
  {
    return self::$classMap[$class][$method] ?? null;
  }
  public static function removeForClass(string $class, string $method): void
  {
    unset(self::$classMap[$class][$method]);
  }
  public static function setForFunction(string $fnName, callable $fn): void
  {
    self::$fnMap[$fnName] = $fn;
  }
  public static function getForFunction(string $fn): ?callable
  {
    return self::$fnMap[$fn] ?? null;
  }
  /** @return string[] */
  public static function getFunctions(): array
  {
    return array_keys(self::$fnMap);
  }
  public static function removeForFunction(string $fn): void
  {
    unset(self::$fnMap[$fn]);
  }
  public static function clean(): void
  {
    self::$classMap = [];
    self::$fnMap = [];
  }
}