<?php


namespace AspectOverride\Facades;

use AspectOverride\Core\Configuration;

/**
 * @method static void initialize(Configuration $configuration)
 * @method static Configuration getConfiguration()
 */
class Instance
{
    /** @var \AspectOverride\Core\Instance */
    public static $instance;

    public static function __callStatic($name, $arguments)
    {
        if (!isset(self::$instance)) {
            self::$instance = new \AspectOverride\Core\Instance();
        }
        return self::$instance->$name(...$arguments);
    }
}