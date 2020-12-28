<?php

namespace AspectOverride\Loader;

use AspectOverride\Core\Core;
use AspectOverride\Core\Registry;
use AspectOverride\Mocking\ClassMocker;
use AspectOverride\Mocking\FunctionMocker;
use AspectOverride\Util\ClassUtils;
use Composer\Autoload\ClassLoader;

final class AutoloaderWrapper
{
  /** @var ClassLoader */
  protected $composerLoader;
  /** @var string[] */
  protected $configuredDirectories;
  /** @var ClassMocker */
  protected $classMocker;

  public function __construct(ClassMocker $classMocker = null) 
  {
    $this->configuredDirectories = Core::getInstance()->getDirectories() ?? [];
    $this->classMocker = $classMocker ?? new ClassMocker();
  }
  public function setAutoloader(ClassLoader $classLoader): self
  {
    $this->composerLoader = $classLoader;
    // Make sure these classes are loaded
    $this->composerLoader->loadClass(FunctionMocker::class);
    $this->composerLoader->loadClass(ClassUtils::class);
    $this->composerLoader->loadClass(Registry::class);
    return $this;
  }
  /** @return bool|null */
  public function loadClass(string $class)
  {
    $path = realpath($this->composerLoader->findFile($class) ?: '');
    if(!$path) {
      return false;
    }
    FunctionMocker::loadFunctions($class);
    if($this->isInConfiguredDirectories($path)) {
      $this->classMocker->loadMocked($path);
      return true;
    }
    return $this->composerLoader->loadClass($class);
  }
  protected function isInConfiguredDirectories(string $path): bool
  {
    foreach($this->configuredDirectories as $directory) {
      if(false !== strpos($path, $directory)) {
        return true;
      }
    }
    return false;
  }
}
