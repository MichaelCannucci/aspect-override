<?php

namespace AspectOverride\Mocking;

use AspectOverride\Core\Registry;
use AspectOverride\Util\ClassUtils;

class FunctionMocker
{
  public static function loadFunctions(string $class): void 
  {
    foreach(Registry::getFunctions() as $function) {
      $namespace = ClassUtils::getNamespace($class);
      $code = <<<EOL
      namespace {$namespace} {
        if(!function_exists('$namespace\\$function')) {
          function {$function}() {
            if(\$__fn__ = \AspectOverride\Core\Registry::getForFunction('$function')) {
              return \$__fn__(...func_get_args());
            }
          }
        }
      }
      EOL;
      eval($code);
    }
  }
}