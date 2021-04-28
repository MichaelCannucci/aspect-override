<?php

use AspectOverride\Override;
use Tests\Util\TestClasses\CachedClass;
use Tests\Util\TestClasses\MultipleMethods;

class CachedMockerTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \AspectOverride\Facades\Instance::getConfiguration()->setShouldUseCache(true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \AspectOverride\Facades\Instance::getConfiguration()->setShouldUseCache(false);
    }

    public function test_can_load_class_from_cache()
    {
        $class = new CachedClass();
        Override::method(CachedClass::class, "run", function () {
            return true;
        });
        $this->assertTrue($class->run());
    }
}