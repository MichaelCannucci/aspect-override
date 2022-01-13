<?php

namespace AspectOverride;

use AspectOverride\Facades\Instance;
use AspectOverride\Facades\Registry;

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
        Instance::getInstance()->getRegistry()->setForClass($class, $method, $override);
        return function () use ($class, $method) {
            Instance::getInstance()->getRegistry()->removeForClass($class, $method);
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
        Instance::getInstance()->getRegistry()->setForFunction($fn, $override);
        return function () use ($fn) {
            Instance::getInstance()->getRegistry()->removeForFunction($fn);
        };
    }

    /**
     * Ensure that the function is always overwritten
     * @param string $fn function name
     * @return void 
     */
    public static function reserve(string $fn): void
    {
        Instance::getInstance()->getRegistry()->setForFunction($fn, function () {
        });
    }

    /**
     * Remove all the overrides
     * @return void
     */
    public static function reset(): void
    {
        Instance::getInstance()->getRegistry()->reset();
    }
}
