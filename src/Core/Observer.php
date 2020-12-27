<?php

namespace AspectOverride\Core;

class Observer
{
  const CLASS_LOADED = 'class_loaded';

  /** @var array<string,array<string,callable>> */
  protected static $events = [];
  protected static $id = 0;

  public static function subscribe(string $event, string $id, callable $handler): void
  {
    self::$events[$event][$id] = $handler;
  }
  public static function unsubscribe(string $event, $id): void
  {
    if(isset(self::$events[$event][$id])) {
      unset(self::$events[$event][$id]);
    }
  }
  public static function reset(): void
  {
    self::$events = [];
  }
  /** @param mixed $data */
  public static function dispatch(string $event, $data): void
  {
    foreach ((self::$events[$event] ?? []) as $handler)
    {
      ($handler)($data);
    }
  }
}