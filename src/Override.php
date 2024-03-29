<?php

namespace AspectOverride;

use AspectOverride\Facades\AspectOverride;

class Override {
    /**
     * Override a method's implementation with the provided callable
     * @psalm-param class-string $class
     * @param callable $override callable which match the arguments of the function and returns a value
     * @return callable Function to unregister the override
     */
    public static function method(string $class, string $method, callable $override): callable {
        AspectOverride::getInstance()->getClassRegistry()->set($class, $method, function ($execute, ...$args) use ($override) {
            // Ignore original
            return $override(...$args);
        });
        return function () use ($class, $method) {
            AspectOverride::getInstance()->getClassRegistry()->remove($class, $method);
        };
    }

    /**
     * Modify a methods arguments before the method runs
     * @psalm-param class-string $class
     * @return callable():array callable which match the arguments of the function and the returned array is the arguments to be used instead
     * @return callable Function to unregister the override
     */
    public static function before(string $class, string $method, callable $override): callable {
        AspectOverride::getInstance()->getClassRegistry()->set($class, $method, function ($execute, &...$args) use ($override) {
            $override(...$args);
            return $execute(...$args);
        });
        return function () use ($class, $method) {
            AspectOverride::getInstance()->getClassRegistry()->remove($class, $method);
        };
    }

    /**
     * Modify a methods return after function runs
     * @psalm-param class-string $class
     * @return callable(mixed):mixed callable which has an argument for the functions return and return the modified return
     * @return callable Function to unregister the override
     */
    public static function after(string $class, string $method, callable $override): callable {
        AspectOverride::getInstance()->getClassRegistry()->set($class, $method, function ($execute, ...$args) use ($override) {
            $result = $execute(...$args);
            return $override($result);
        });
        return function () use ($class, $method) {
            AspectOverride::getInstance()->getClassRegistry()->remove($class, $method);
        };
    }

    /**
     * Override a function's execution
     * @param string $fn
     * @param callable $override
     * @return callable Function to unregister the override
     */
    public static function function(string $fn, callable $override): callable {
        AspectOverride::getInstance()->getFunctionRegistry()->set($fn, $override);
        return function () use ($fn) {
            AspectOverride::getInstance()->getFunctionRegistry()->remove($fn);
        };
    }

    /**
     * Remove all the overrides
     * @return void
     */
    public static function reset(): void {
        AspectOverride::getInstance()->resetRegistry();
    }
}
