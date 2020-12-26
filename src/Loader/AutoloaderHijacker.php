<?php

namespace AspectOverride\Loader;

use AspectOverride\Loader\AutoloaderWrapper;

class AutoloaderHijacker
{
  public static function hijack($class, AutoloaderWrapper $wrapper)
  {
    $autoloaders = spl_autoload_functions();
    foreach ($autoloaders as $registeredLoader) {
      if (is_array($registeredLoader) && $registeredLoader[0] instanceof $class) {
        $composerAutoLoader = $registeredLoader;
        spl_autoload_unregister($composerAutoLoader);
        $composerAutoLoader[0] = $wrapper->setAutoloader($composerAutoLoader[0]);
        spl_autoload_register($composerAutoLoader, true, true);
        return;
      }
    }
    throw new \RuntimeException("Composer's Autoloader was never registered!");
  }
}