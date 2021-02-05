<?php

namespace AspectOverride\Loader;

use AspectOverride\Loader\AutoloaderWrapper;
use Composer\Autoload\ClassLoader;
use Exception;
use ReflectionFunction;

class AutoloaderHijacker
{
  /** @var array<class-string,mixed> */
  protected static $originalClassLoaders = [];
  /** @var array<string,mixed> */
  protected static $originalMethodLoaders = [];

  public static function restore(): void
  {
    self::clearOurLoaders();
    foreach(self::$originalClassLoaders as $classLoader) {
      spl_autoload_register($classLoader);
    }
  }
  public static function restoreMethods(): void
  {
    self::clearOurLoaders();
    foreach(self::$originalMethodLoaders as $method) {
      spl_autoload_register($method);
    }
  }
  protected static function clearOurLoaders(): void
  {
    $autoloaders = spl_autoload_functions();
    foreach($autoloaders as $registeredLoader) {
      if(is_array($registeredLoader) && $registeredLoader[0] instanceof AutoloaderWrapper) {
        /** @phpstan-ignore-next-line */
        spl_autoload_unregister($registeredLoader);
      }
    }
  }
  public static function hijackComposer(AutoloaderWrapper $wrapper): void
  {
    $autoloaders = spl_autoload_functions();
    foreach ($autoloaders as $registeredLoader) {
      if (is_array($registeredLoader) && $registeredLoader[0] instanceof ClassLoader) {
        self::$originalClassLoaders[get_class($registeredLoader[0])] = $registeredLoader;
        /** @phpstan-ignore-next-line type-hint for array callback */
        spl_autoload_unregister($registeredLoader);
        $registeredLoader[0] = $wrapper->setAutoloader($registeredLoader[0]);
        /** @phpstan-ignore-next-line type-hint for array callback */
        spl_autoload_register($registeredLoader, true, true);
        return;
      }
    }
    throw new \RuntimeException("Composer Autoloader was never registered!");
  }
  public static function hijackMethod(string $file, callable $fileResolver, AutoloaderWrapper $wrapper): void
  {
    $autoloaders = spl_autoload_functions();
    foreach($autoloaders as $registeredAutoLoader)
    {
      if(
        !is_array($registeredAutoLoader) 
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