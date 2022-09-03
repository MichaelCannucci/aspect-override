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

    public static function debugDump($data): void {
        if($path = Instance::getConfiguration()->getDebugDump()) {
            $name = md5($data);
            file_put_contents("$path/$name.php", $data);
        }
    }

    public static function initialize(Configuration $configuration): void {
        self::$instance = new CoreInstance($configuration);
        self::$instance->reset();
        self::$instance->start();
    }

    public static function getForFunction(string $fn): ?callable {
        return self::getInstance()->getFunctionRegistry()->get($fn);
    }

    /**
     * @param class-string $class
     * @param string $method
     * @param mixed[] $args
     * @param callable $execute
     * @return mixed
     */
    public static function wrapAround(string $class, string $method, array $args, callable $execute) {
        $stub = function (callable $execute, $args) {
            return $execute(...$args);
        };
        $around = self::getInstance()->getClassRegistry()->get($class, $method) ?? $stub;
        return $around($execute, $args);
    }
}
