<?php

namespace AspectOverride;

use AspectOverride\Core\Observer;
use AspectOverride\Core\Registry;
use AspectOverride\Mocking\FunctionMocker;
use AspectOverride\Mocking\ScopedTracker;

class Override 
{
   /**
   * @param class-string $class
   */
   public static function method(string $class, string $method, callable $override): void
   {
      Registry::setForClass($class, $method, $override);
   }
   public static function function(string $fn, callable $override): ScopedTracker
   {
      Registry::setForFunction($fn, $override);
      FunctionMocker::subscribeToLoading($fn);
      return new ScopedTracker($fn);
   }
   public static function clean(): void
   {
      Registry::clean();
   }
}