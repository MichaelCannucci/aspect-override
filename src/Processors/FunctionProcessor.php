<?php

namespace AspectOverride\Processors;

class FunctionProcessor extends AbstractProcessor {
    public const NAME = 'aspect_mock_function_override';

    public const PATTERN = '/(?<!new|function)(\s|\()(((?!function|if|else|elseif)\w+)(\())/m';

    public const NAMESPACE_PATTERN = '/namespace (.+)(;| {)/m';

    /** @var string[] */
    private $namespaces = [];

    /** Function that shouldn't be patched because it breaks things or doesn't make sense */
    private const DENY_LIST = [
        'extract' => true // extract does nothing because it runs in another scope, so no variables change
    ];

    public function onNewFile(): void {
        $this->namespaces = [''];
    }

    public function transform(string $data): string {
        preg_match_all(self::NAMESPACE_PATTERN, $data, $found);
        if ($found) {
            $this->namespaces = $found[1];
        }
        return (string)preg_replace_callback(self::PATTERN, function ($m) {
            $function = $m[3];
            if (!array_key_exists($function, self::DENY_LIST) && $this->functionExists($function)) {
                $function = $this->loadPatchedFunction($function);
            }
            return $m[1] . $function . $m[4];
        }, $data);
    }

    public function loadPatchedFunction(string $name): string {
        $uniqueName = $name . '_' . md5($name);
        foreach ($this->namespaces as $namespace) {
            [$parameters, $passed] = $this->getOriginalParameters($namespace, $name);
            $code = /** @lang PHP */ "
              namespace $namespace {
               if(!function_exists('$namespace\\$uniqueName')) {
                 function {$uniqueName}($parameters) {
                   \$__fn__ =  \AspectOverride\Facades\Instance::getForFunction('$namespace\\$name') ?: 
                      \AspectOverride\Facades\Instance::getForFunction('$name') ?: null; 
                   if(\$__fn__) {
                     return \$__fn__($passed);
                   }
                   return $name($passed);
                 }
               }
            }";
            eval($code);
        }
        return $uniqueName;
    }

    /**
     * @return array{0:string,1:string}
     */
    public function getOriginalParameters(string $namespace, string $name): array {
        try {
            $namespaced = $namespace . '\\' . $name;
            $function = function_exists($namespaced) ? $namespaced : $name;
            $reflection = new \ReflectionFunction($function);
            $functionParameters = [];
            $passedParameters = [];
            foreach ($reflection->getParameters() as $parameter) {
                $ref = $parameter->isPassedByReference() ? '&' : '';
                $default = $parameter->isDefaultValueAvailable() ? ' = ' . $this->getDefaultForCode($parameter) : '';
                $variadic = $parameter->isVariadic() ? '...' : '';
                $functionParameters[] = $ref . $variadic . '$' . $parameter->getName() . $default;
                $passedParameters[] = '$' . $parameter->getName();
            }
            return [implode(',', $functionParameters), implode(',', $passedParameters)];
        } catch (\ReflectionException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    protected function functionExists(string $function): bool {
      if(function_exists($function)) {
        return true;
      }
      foreach ($this->namespaces as $namespace) {
        if(function_exists($namespace . '\\' . $function)) {
          return true;
        }
      }
      return false;
    }

    /**
     * @throws \ReflectionException
     */
    private function getDefaultForCode(\ReflectionParameter $parameter): string {
        $default = $parameter->getDefaultValue();
        if (is_string($default)) {
            return "'$default'";
        } elseif (null === $default) {
            return 'null';
        }
        return (string)$default;
    }
}
