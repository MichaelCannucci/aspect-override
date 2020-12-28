<?php

namespace AspectOverride;

use AspectOverride\Core\Registry;

class Override 
{
   /**
   * Override a class's method
   * ex: MyClass::run will now echo 'Works!'
   * @param class-string $class
   * @return callable Function to unregister the override
   */
   public static function method(string $class, string $method, callable $override): callable
   {
      Registry::setForClass($class, $method, $override);
      return function() use ($class, $method)  {
         Registry::removeForClass($class, $method);
      };
   }
   /**
    * Override a global function's execution
    * ex: time() should return 10
    * @param string $fn 
    * @param callable $override 
    * @return callable Function to unregister the override 
    */
   public static function function(string $fn, callable $override): callable
   {
      Registry::setForFunction($fn, $override);
      return function() use ($fn) {
         Registry::removeForFunction($fn);
      };
   }
   /**
    * Remove all the overrides
    * @return void
    */
   public static function clean(): void
   {
      Registry::clean();
   }
}