<?php

namespace AspectOverride\Facades;

use AspectOverride\Core\ClassRegistry;
use AspectOverride\Core\Configuration;
use AspectOverride\Core\FunctionRegistry;
use AspectOverride\Core\Instance;
use AspectOverride\Core\StreamInterceptor;

/**
 * @method static Instance reset()
 * @method static Instance start()
 * @method static Configuration getConfiguration()
 * @method static ClassRegistry getClassRegistry()
 * @method static FunctionRegistry getFunctionRegistry()
 * @method static StreamInterceptor getStreamInterceptor()
 * @method static Instance resetRegistry()
 * @method static callable|null getForFunction(string $fn)
 * @method static callable|null wrapAround(string $class, string $method, array $args, callable $execute)
 * @method static bool shouldProcess(string $path)
 */
class AspectOverride {
    /** @var Instance|null */
    public static $instance = null;

    /**
     * @param mixed $name
     * @param mixed $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments) {
        return self::getInstance()->$name(...$arguments);
    }

    public static function setInstance(?Instance $instance): void {
        self::$instance = $instance;
    }

    public static function getInstance(): Instance {
        if (!self::$instance) {
            throw new \RuntimeException("aspect-override has not been initalized, call AspectOverride\Facades\Instance::initialize()");
        }
        return self::$instance;
    }

    public static function initialize(Configuration $configuration): void {
        self::$instance = new Instance($configuration);
    }
}
