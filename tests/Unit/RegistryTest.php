<?php

use AspectOverride\Core\ClassRegistry;
use TRegx\DataProvider\CrossDataProviders;

$dataProvider = CrossDataProviders::cross([
    ['Test'],
    ['Test\A\B'],
    ['Test\A\B\A']
], [
    ['test']
]);

it("registry properly saves callbacks", function ($class, $method) {
    $registry = new ClassRegistry();
    $registry->set($class, $method, function () {
    });
    $this->assertNotNull($registry->get($class, $method));
})->with($dataProvider);

it("can properly remove callbacks", function ($class, $method) {
    $registry = new ClassRegistry();
    $registry->set($class, $method, function () {
    });
    $registry->remove($class, $method);
    $this->assertNull($registry->get($class, $method));
})->with($dataProvider);