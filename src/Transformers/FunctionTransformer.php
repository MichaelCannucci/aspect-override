<?php

namespace AspectOverride\Transformers;

use AspectOverride\Facades\Registry;

class FunctionTransformer
{
  public static function loadFunctions(string $namespace): void
  {
    foreach (Registry::getFunctions() as $function) {
      $code = "
            namespace {$namespace} {
              if(!function_exists('\\$namespace\\$function')) {
                function {$function}() {
                  if(\$__fn__ = \AspectOverride\Facades\Registry::getForFunction('$function')) {
                    return \$__fn__(...func_get_args());
                  }
                }
              }
            }";
      eval($code);
    }
  }
}
