<?php

namespace AspectOverride\Loader;

use AspectOverride\Core\Instance;
use AspectOverride\Core\Registry;
use AspectOverride\Mocking\ClassMocker;
use AspectOverride\Mocking\FunctionMocker;
use AspectOverride\Util\ClassUtils;
use Composer\Autoload\ClassLoader;

class AutoloaderWrapper
{
  /** @var ClassLoader */
  protected $composerLoader;
  /** @var callable|null */
  protected $autoloaderFunctionFindFile;
  /** @var callable|null */
  protected $autoloaderFunction;
  /** @var ClassMocker */
  protected $classMocker;

  public function __construct(ClassMocker $classMocker = null) 
  {
    $this->classMocker = $classMocker ?? new ClassMocker();
    /// Make sure these static class are loaded before we modify the composer autoloader
    class_exists(FunctionMocker::class);
    class_exists(ClassUtils::class);
    class_exists(Registry::class);
  }
  public function __invoke(string $class): ?bool
  {
    return $this->loadClass($class);
  }
  public function setAutoloaderFunction(callable $fileResolver, callable $original): self
  {
    $this->autoloaderFunctionFindFile = $fileResolver;
    $this->autoloaderFunction = $original;
    return $this;
  }
  public function setAutoloader(ClassLoader $classLoader): self
  {
    $this->composerLoader = $classLoader;
    return $this;
  }
  protected function getPath(string $class): ?string
  {
    if ($this->autoloaderFunctionFindFile) {
      return ($this->autoloaderFunctionFindFile)($class) ?: null;
    }
    return $this->composerLoader->findFile($class) ?: null;
  }
  protected function loadOriginal(string $class): ?bool
  {
    if ($this->autoloaderFunction) {
      return ($this->autoloaderFunction)($class) ?: null;
    }
    return $this->composerLoader->loadClass($class);
  }
  public function loadClass(string $class): ?bool
  {
    $path = $this->getPath($class);
    if(!$path) {
      return false;
    }
    FunctionMocker::loadFunctions($class);
    if($this->isInConfiguredDirectories($path)) {
      $this->classMocker->loadMocked($path);
      return true;
    }
    return $this->loadOriginal($class);
  }
  protected function isInConfiguredDirectories(string $path): bool
  {
    $path = realpath($path) ?: '';
    foreach(Instance::getInstance()->getDirectories() as $directory) {
      if(false !== strpos($path, $directory)) {
        return true;
      }
    }
    return false;
  }
}
