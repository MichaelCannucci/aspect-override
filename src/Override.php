<?php

namespace AspectOverride;

use AspectOverride\Core\Registry;

class Override 
{
   /**
   * @param class-string $class
   */
   public static function method(string $class, string $method, callable $override): void
   {
      Registry::setForClass($class, $method, $override);
   }
   public static function function(string $fn, callable $override): void
   {
      Registry::setForFunction($fn, $override);
   }
}