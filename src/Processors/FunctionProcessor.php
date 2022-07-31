<?php

namespace AspectOverride\Processors;

class FunctionProcessor extends AbstractProcessor {
    public const NAME = 'aspect_mock_function_override';

    public const PATTERN = '/(?<!new|function)(\s|\()(((?!function|if|else|elseif)\w+)(\(.*?\)))/m';

    public const NAMESPACE_PATTERN = '/namespace (.+)(;| {)/m';

    /** @var string[] */
    private $namespaces = [];

    /** Function that shouldn't be patched because it breaks things or doesn't make sense */
    private const DENY_LIST = [
        'extract' => true // extract does nothing because it runs in another scope, so no variables change
    ];

    public function onNewFile(): void {
        $this->namespaces = [];
    }

    public function transform(string $data): string {
        preg_match_all(self::NAMESPACE_PATTERN, $data, $found);
        if ($found) {
            $this->namespaces = $found[1];
        }
        return preg_replace_callback(self::PATTERN, function ($m) {
            $function = $m[3];
            if (!array_key_exists($function, self::DENY_LIST)) {
                $function = $this->loadPatchedFunction($m[3]);
            }
            return $m[1] . $function . $m[4];
        }, $data);
    }

    public function loadPatchedFunction(string $name): string {
        $uniqueName = $name . '_' . md5($name);
        $namespaces = empty($this->namespaces) ? [''] : $this->namespaces;
        foreach ($namespaces as $namespace) {
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

    /**
     * @throws \ReflectionException
     */
    private function getDefaultForCode(\ReflectionParameter $parameter) {
        $default = $parameter->getDefaultValue();
        if (is_string($default)) {
            return "'$default'";
        } elseif (null === $default) {
            return 'null';
        }
        return $default;
    }
}
