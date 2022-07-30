<?php

namespace AspectOverride;

use AspectOverride\Facades\Instance;

class Override
{
    /**
     * Override a class's method
     * ex: MyClass::run will now echo 'Works!'
     * @psalm-param class-string $class
     * @return callable Function to unregister the override
     */
    public static function method(string $class, string $method, callable $override): callable
    {
        Instance::getInstance()->getClassOverwriteRegistry()->set($class, $method, $override);
        return function () use ($class, $method) {
            Instance::getInstance()->getClassOverwriteRegistry()->remove($class, $method);
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
        Instance::getInstance()->getFunctionRegistry()->set($fn, $override);
        return function () use ($fn) {
            Instance::getInstance()->getFunctionRegistry()->remove($fn);
        };
    }

    /**
     * Ensure that the function is always overwritten
     * @param string $fn function name
     * @return void 
     */
    public static function reserve(string $fn): void
    {
        Instance::getInstance()->getFunctionRegistry()->set($fn, function () {
        });
    }

    /**
     * Remove all the overrides
     * @return void
     */
    public static function reset(): void
    {
        Instance::getInstance()->resetRegistry();
    }
}
