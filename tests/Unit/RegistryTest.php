<?php

namespace Tests\Unit;

use AspectOverride\Core\ClassRegistry;
use PHPUnit\Framework\TestCase;
use TRegx\DataProvider\CrossDataProviders;

class RegistryTest extends TestCase {
    /**
     * @dataProvider class_provider
     */
    public function test_can_save_callback(string $class, string $method) {
        $registry = new ClassRegistry();
        $registry->set($class, $method, function () {
        });
        $this->assertNotNull($registry->get($class, $method));
    }

    /**
     * @dataProvider class_provider
     */
    public function test_can_remove_callback(string $class, string $method) {
        $registry = new ClassRegistry();
        $registry->set($class, $method, function () {
        });
        $registry->remove($class, $method);
        $this->assertNull($registry->get($class, $method));
    }

    public function class_provider() {
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
