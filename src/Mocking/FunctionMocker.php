<?php

namespace AspectOverride\Mocking;

use AspectOverride\Util\ClassUtils;

class FunctionMocker
{
  public static function loadMocked(string $class, string $fn): void 
  {
    $namespace = ClassUtils::escapeFQN(ClassUtils::getNamespace($class));
    $code = <<<EOL
    namespace {$namespace} {
      if(!function_exists('$namespace\\$fn')) {
        function {$fn}() {
          if(\$__fn__ = \AspectOverride\Core\Registry::getForFunction('$fn')) {
            return \$__fn__(func_get_args());
          }
        }
      }
    }
    EOL;
    eval($code);
  }
}