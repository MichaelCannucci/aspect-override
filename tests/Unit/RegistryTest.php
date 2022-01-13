<?php

namespace Tests\Unit;

use AspectOverride\Core\Registry;
use PHPUnit\Framework\TestCase;
use TRegx\DataProvider\CrossDataProviders;

class RegistryTest extends TestCase
{
    /**
     * @dataProvider class_provider
     */
    public function test_can_save_callback(string $class, string $method)
    {
        $registry = new Registry();
        $registry->setForClass($class, $method, function () {
        });
        $this->assertNotNull($registry->getForClass($class, $method));
    }

    /**
     * @dataProvider class_provider
     */
    public function test_can_remove_callback(string $class, string $method)
    {
        $registry = new Registry();
        $registry->setForClass($class, $method, function () {
        });
        $registry->removeForClass($class, $method);
        $this->assertNull($registry->getForClass($class, $method));
    }

    public function class_provider()
    {
        return CrossDataProviders::cross(
            [
                ['Test'],
                ['Test\A\B'],
                ['Test\A\B\A']
            ],
            [
                ['test']
            ]
        );
    }
}
