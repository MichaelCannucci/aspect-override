<?php

namespace AspectOverride;

use AspectOverride\Facades\Instance;

class Override {
    /**
     * Override a method's implementation with the provided callable
     * @psalm-param class-string $class
     * @param callable $override callable which match the arguments of the function and returns a value
     * @return callable Function to unregister the override
     */
    public static function method(string $class, string $method, callable $override): callable {
        Instance::getInstance()->getClassRegistry()->set($class, $method, function($args, $execute) use ($override) {
            // Ignore original
            return $override($args);
        });
        return function () use ($class, $method) {
            Instance::getInstance()->getClassRegistry()->remove($class, $method);
        };
    }

    /**
     * Modify a methods arguments before the method runs
     * @psalm-param class-string $class
     * @return callable():array callable which match the arguments of the function and the returned array is the arguments to be used instead
     * @return callable Function to unregister the override
     */
    public static function beforeMethod(string $class, string $method, callable $override): callable {
        Instance::getInstance()->getClassRegistry()->set($class, $method, function($args, $execute) use ($override) {
            $newArgs = $override($args);
            return $execute($newArgs);
        });
        return function () use ($class, $method) {
            Instance::getInstance()->getClassRegistry()->remove($class, $method);
        };
    }

    /**
     * Modify a methods return after function runs
     * @psalm-param class-string $class
     * @return callable(mixed):mixed callable which has an argument for the functions return and return the modified return
     * @return callable Function to unregister the override
     */
    public static function afterMethod(string $class, string $method, callable $override): callable {
        Instance::getInstance()->getClassRegistry()->set($class, $method, function($args, $execute) use ($override) {
            $result = $execute($args);
            return $override($result);
        });
        return function () use ($class, $method) {
            Instance::getInstance()->getClassRegistry()->remove($class, $method);
        };
    }

    /**
     * Override a function's execution
     * @param string $fn
     * @param callable $override
     * @return callable Function to unregister the override
     */
    public static function function(string $fn, callable $override): callable {
        Instance::getInstance()->getFunctionRegistry()->set($fn, $override);
        return function () use ($fn) {
            Instance::getInstance()->getFunctionRegistry()->remove($fn);
        };
    }

    /**
     * Remove all the overrides
     * @return void
     */
    public static function reset(): void {
        Instance::getInstance()->resetRegistry();
    }
}
