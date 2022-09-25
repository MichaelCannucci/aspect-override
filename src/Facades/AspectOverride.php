<?php

namespace AspectOverride\Facades;

use AspectOverride\Core\Configuration;
use AspectOverride\Core\Instance;

/**
 * @method static Configuration getConfiguration()
 * @method static bool shouldProcess(string $uri)
 * @method static void dump(string $data)
 * @method static Instance resetRegistry()
 * @method static callable|null getForFunction(string $fn)
 * @method static callable|null wrapAround(string $class, string $method, array $args, callable $execute)
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
        self::$instance->reset();
        self::$instance->start();
    }
}
