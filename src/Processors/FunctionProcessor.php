<?php

namespace AspectOverride\Processors;

class FunctionProcessor extends AbstractProcessor {
    public const NAME = 'aspect_mock_function_override';

    public const PATTERN = '/(function |new )?(\w+)(\()/i';

    public const NAMESPACE_PATTERN = '/namespace (.+)(;| {)/m';

    /** @var string[] */
    private $namespaces = [];

    /** Function that shouldn't be patched because it breaks things or doesn't make sense */
    private const DENY_LIST = [
        'extract'      => true, // extract does nothing because it runs in another scope, so no variables change
        'if'           => true, // Language Keyword
        'elseif'       => true, // Language Keyword
        'else'         => true, // Language Keyword
        'function'     => true, // Language Keyword
        'while'        => true, // Language Keyword
        'unset'        => true, // Language Keyword
        'isset'        => true, // Language Keyword
        'empty'        => true, // Language Keyword
        'die'          => true, // Language Keyword
        'use'          => true, // Language Keyword
        'match'        => true, // Language Keyword
        'declare'      => true, // Language Keyword
        'list'         => true, // Language Keyword
        'array'        => true, // Language Keyword
        'require'      => true, // Language Keyword
        'require_once' => true, // Language Keyword
        'include'      => true, // Language Keyword
        'include_once' => true, // Language Keyword
        'echo'         => true, // Language Keyword
    ];

    public function onNewFile(): void {
        $this->namespaces = [];
    }

    public function transform(string $data): string {
        preg_match_all(self::NAMESPACE_PATTERN, $data, $found);
        if ($found) {
            $this->namespaces = $found[1];
        }
        return (string)preg_replace_callback(self::PATTERN, function ($m) {
            [$original, $before, $function, $after] = $m;
            if (
                !array_key_exists($function, self::DENY_LIST)
                && !in_array(trim(mb_convert_case($before, MB_CASE_LOWER)), ['function', 'new'])
                && $this->functionExists($function)
            ) {
                $function = $this->loadPatchedFunction($function);
                return $before . $function . $after;
            }
            return $original;
        }, $data);
    }

    public function loadPatchedFunction(string $name): string {
        $uniqueName = $name . '_' . md5($name);
        foreach (($this->namespaces ?: ['']) as $namespace) {
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
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
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
