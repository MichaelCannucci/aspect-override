<?php

namespace Tests\Unit;

use AspectOverride\Loader\AutoloaderHijacker;
use AspectOverride\Loader\AutoloaderWrapper;
use PHPUnit\Framework\TestCase;
use ReflectionFunction;

class HijackAutoloaderTest extends TestCase
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
  public function test_can_override_spl_autoloader_functions()
  {
    $autoloaderFunctionFile = __DIR__ . '/../Util/FunctionAutoloader.php';
    require_once $autoloaderFunctionFile;
    $fakeWrapper = new class extends AutoloaderWrapper {
      public function loadClass(string $class): ?bool { return false; }
    };
    $fileResolver = function() { return ''; };
    AutoloaderHijacker::hijackMethod($autoloaderFunctionFile, $fileResolver, $fakeWrapper); 
    $functions = spl_autoload_functions();
    foreach ($functions as $function) {
      if (!is_array($function) && is_callable($function)) {
        if (!($function instanceof \Closure)) {
          $function = \Closure::fromCallable($function);
        }
        $reflection = new ReflectionFunction($function);
        if (false !== strpos($reflection->getFileName(), 'AutoloaderWrapper.php')) {
          $this->assertTrue(true);
          return;
        }
      }
    }
    $this->assertTrue(false);
  }
}
