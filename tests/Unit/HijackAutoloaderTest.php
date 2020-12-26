<?php

namespace Tests\Unit;

use AspectOverride\Loader\AutoloaderWrapper;
use PHPUnit\Framework\TestCase;

class HijackAutoloader extends TestCase
{
  public function test_autoloader_successfully_wrapped()
  {
    // Bootstrap should have loaded it, we're just checking
    $loaders = spl_autoload_functions();
    foreach($loaders as $loader) {
      if(is_array($loader) && $loader[0] instanceof AutoloaderWrapper) {
        $this->assertTrue(true);
        return;
      }
    }
    $this->assertTrue(false);
  }
}
