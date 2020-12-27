<?php

namespace AspectOverride;

use AspectOverride\Core\Observer;
use AspectOverride\Core\Registry;
use AspectOverride\Mocking\FunctionMocker;
use AspectOverride\Util\ClassUtils;

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
      Observer::subscribe(Observer::CLASS_LOADED, $fn, function(string $class) use ($fn) {
         FunctionMocker::loadMocked($class, $fn);
      });
   }
}