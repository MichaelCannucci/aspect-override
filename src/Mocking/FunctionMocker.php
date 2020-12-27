<?php

namespace AspectOverride\Mocking;

use AspectOverride\Util\ClassUtils;

class FunctionMocker
{
  /** @var array<string,true> */
  protected static $functions = [];
  public static function subscribeToLoading(string $fn): void
  {
    // Makes it easier to remove if it's a key-value
    self::$functions[$fn] = true;
  }
  public static function unsubscribeToLoading(string $fn): void
  {
    if(isset(self::$functions[$fn])) {
      unset(self::$functions[$fn]);
    }
  }
  public static function loadFunctions(string $class): void 
  {
    foreach(self::$functions as $function => $_) {
      $namespace = ClassUtils::escapeFQN(ClassUtils::getNamespace($class));
      $code = <<<EOL
      namespace {$namespace} {
        if(!function_exists('$namespace\\$function')) {
          function {$function}() {
            if(\$__fn__ = \AspectOverride\Core\Registry::getForFunction('$function')) {
              return \$__fn__(func_get_args());
            }
          }
        }
      }
      EOL;
      eval($code);
    }
  }
}