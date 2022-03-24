<?php


namespace AspectOverride\Facades;

use AspectOverride\Core\Configuration;
use AspectOverride\Core\Instance as CoreInstance;

/**
 * @method static Configuration getConfiguration()
 */
class Instance
{
    /** @var \AspectOverride\Core\Instance|null */
    public static $instance = null;

    /**
     * @param mixed $name
     * @param mixed $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return self::getInstance()->$name(...$arguments);
    }

    public static function getInstance(): CoreInstance
    {
        if (!self::$instance) {
            throw new \RuntimeException("aspect-override has not been initalized, call AspectOverride\Facades\Instance::initialize()");
        }
        return self::$instance;
    }

    public static function initialize(Configuration $configuration): void
    {
        self::$instance = new CoreInstance($configuration);
        self::$instance->reset();
        self::$instance->start();
    }
}
