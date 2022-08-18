<?php

namespace AspectOverride\Facades;

use AspectOverride\Core\Configuration;
use AspectOverride\Core\Instance as CoreInstance;

/**
 * @method static Configuration getConfiguration()
 */
class Instance {
    /** @var \AspectOverride\Core\Instance|null */
    public static $instance = null;

    /**
     * @param mixed $name
     * @param mixed $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments) {
        return self::getInstance()->$name(...$arguments);
    }

    public static function getInstance(): CoreInstance {
        if (!self::$instance) {
            throw new \RuntimeException("aspect-override has not been initalized, call AspectOverride\Facades\Instance::initialize()");
        }
        return self::$instance;
    }

    public static function getForFunction(string $fn): ?callable {
        return self::getInstance()->getFunctionRegistry()->get($fn);
    }

    /**
     * @param class-string $class
     * @return mixed
     */
    public static function wrapAround(string $class, string $method, array $args, callable $execute) {
        $stub = function(array $args, callable $execute) { return $execute($args); };
        $around = self::getInstance()->getClassBeforeRegistry()->get($class, $method) ?? $stub;
        return $around($args, $execute);
    }

    public static function initialize(Configuration $configuration): void {
        self::$instance = new CoreInstance($configuration);
        self::$instance->reset();
        self::$instance->start();
    }
}
