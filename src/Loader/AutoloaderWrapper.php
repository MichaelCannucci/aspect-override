<?php

namespace AspectOverride\Loader;

use AspectOverride\Core\Core;
use AspectOverride\Mocking\ClassMocker;
use AspectOverride\Mocking\MockCreatorInterface;
use Composer\Autoload\ClassLoader;

final class AutoloaderWrapper
{
  /** @var ClassLoader */
  protected $composerLoader;
  /** @var string[] */
  protected $configuredDirectories;
  /** @var MockCreatorInterface */
  protected $classMocker;

  public function __construct(
    MockCreatorInterface $classMocker = null
  ) 
  {
    $this->configuredDirectories = Core::getInstance()->getDirectories() ?? [];
    $this->classMocker = $classMocker ?? new ClassMocker();
  }
  public function setAutoloader(ClassLoader $classLoader): self
  {
    $this->composerLoader = $classLoader;
    return $this;
  }
  public function loadClass($class)
  {
    $path = $this->composerLoader->findFile($class);
    if(!$path) {
      return false;
    }
    $path = realpath($path);
    if($this->isInConfiguredDirectories($path)) {
      $this->classMocker->loadMocked($path);
      return true;
    }
    return $this->composerLoader->loadClass($class);
  }
  protected function isInConfiguredDirectories($path)
  {
    foreach($this->configuredDirectories as $directory) {
      if(false !== strpos($path, $directory)) {
        return true;
      }
    }
    return false;
  }
}
