<?php

namespace AspectOverride\Processors;

use AspectOverride\Facades\Instance;

class FunctionProcessor extends AbstractProcessor
{
  public const NAME = 'aspect_mock_function_override';

  public const PATTERN = '/namespace (.+)(;| {)/m';

  private const NAMESPACE_INDEX = 1;

  public function transform(string $data): string
  {
    preg_match_all(self::PATTERN, $data, $matches);
    foreach ($matches[self::NAMESPACE_INDEX] as $namespace) {
        $this->declareFunctionInNamespace($namespace);
    }
    return $data;
  }

  public function declareFunctionInNamespace(string $namespace): void
  {
    foreach (Instance::getInstance()->getFunctionRegistry()->getFunctions() as $function) {
      $code = /** @lang InjectablePHP */ "
      namespace {$namespace} {
        if(!function_exists('\\$namespace\\$function')) {
          function {$function}() {
            if(\$__fn__ = \AspectOverride\Facades\Instance::getForFunction('$function')) {
              return \$__fn__(...func_get_args());
            }
          }
        }
      }";
      eval($code);
    }
  }
}
