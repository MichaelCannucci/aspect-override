<?php

namespace AspectOverride\Loader;

use AspectOverride\Loader\AutoloaderWrapper;
use Exception;
use ReflectionFunction;

class AutoloaderHijacker
{
  public static function hijack(string $class, AutoloaderWrapper $wrapper): void
  {
    $autoloaders = spl_autoload_functions();
    foreach ($autoloaders as $registeredLoader) {
      if (is_array($registeredLoader) && $registeredLoader[0] instanceof $class) {
        $composerAutoLoader = $registeredLoader;
        /** @phpstan-ignore-next-line type-hint for array callback */
        spl_autoload_unregister($composerAutoLoader);
        $composerAutoLoader[0] = $wrapper->setAutoloader($composerAutoLoader[0]);
        /** @phpstan-ignore-next-line type-hint for array callback */
        spl_autoload_register($composerAutoLoader, true, true);
        return;
      }
    }
    throw new \RuntimeException("Composer's Autoloader was never registered!");
  }
  public static function hijackMethod(string $file, callable $fileResolver, AutoloaderWrapper $wrapper): void
  {
    $autoloaders = spl_autoload_functions();
    foreach($autoloaders as $registeredAutoLoader)
    {
      if(!is_array($registeredAutoLoader) 
          && is_callable($registeredAutoLoader)
      ) {
        if(!($registeredAutoLoader instanceof \Closure)) {
          $registeredAutoLoader = \Closure::fromCallable($registeredAutoLoader);
        }
        $method = new ReflectionFunction($registeredAutoLoader);
        if ($method->getFileName() === realpath($file)) {
          $wrapper->setAutoloaderFunction($fileResolver, $registeredAutoLoader);
          spl_autoload_unregister($registeredAutoLoader);
          /** @phpstan-ignore-next-line $wrapper has __invoke so it acts like a callable*/
          spl_autoload_register($wrapper);
          return;
        }
      }
    }
    throw new Exception("Can not find autoloader for $file");
  }
}