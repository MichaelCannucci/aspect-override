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

    public static function getOverwriteForClass(string $class, string $method): ?callable {
        return self::getInstance()->getClassOverwriteRegistry()->get($class, $method);
    }

    public static function wrapArguments(string $class, string $method, array $argNames, ...$args): ?array {
        $before = self::getInstance()->getClassBeforeRegistry()->get($class, $method);
        $isList = static function (array $array) {
            $keys = array_keys($array);
            return array_keys($keys) === $keys;
        };
        if ($before) {
            $results = $before(...$args);
            if ($isList($results)) {
                return array_combine($argNames, $results);
            }
            return $results;
        }
        return null;
    }

    /** @return mixed */
    public static function wrapReturn(string $class, string $method, $value) {
        $after = self::getInstance()->getClassAfterRegistry()->get($class, $method);
        if ($after) {
            $value = $after($value);
        }
        return $value;
    }

    public static function initialize(Configuration $configuration): void {
        self::$instance = new CoreInstance($configuration);
        self::$instance->reset();
        self::$instance->start();
    }
}
