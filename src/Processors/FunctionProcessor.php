<?php

namespace AspectOverride\Processors;

use AspectOverride\Facades\Registry;

class FunctionProcessor extends AbstractProcessor
{
  public const NAME = 'aspect_mock_function_override';

  public const PATTERN = '/namespace (.+)(;)/m';

  private const NAMESPACE_INDEX = 1;

  public function transform(string $data): string
  {
    preg_match(self::PATTERN, $data, $matches);
    if (isset($matches[self::NAMESPACE_INDEX])) {
      $this->loadFunctions($matches[self::NAMESPACE_INDEX]);
    }
    return $data;
  }

  public function loadFunctions(string $namespace): void
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
