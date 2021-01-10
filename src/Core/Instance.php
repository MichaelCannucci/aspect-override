<?php

namespace AspectOverride\Core;

use AspectOverride\Loader\AutoloaderHijacker;
use AspectOverride\Loader\AutoloaderWrapper;
use AspectOverride\Util\Configuration;
use Composer\Autoload\ClassLoader;
use RuntimeException;

class Instance
{
  /** @var Configuration */
  protected $config;
  /** @var string[] */
  protected $autoloaders = [ClassLoader::class];
  /** @var string[] */
  protected $autoloaderFiles = [];
  /** @var ?self */
  protected static $instance;

  protected function __construct() { }
  public static function getInstance(): self
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  /**
   * @param array{
   *  directories: string[],
   *  temporaryFilesDir: string,
   *  disableCaching: bool
   * } $options 
   */
  public function init(array $options): void
  {
    $this->config = new Configuration(...$options);
    foreach($this->autoloaders as $loader) {
      AutoloaderHijacker::hijack($loader, new AutoloaderWrapper());
    }
    foreach($this->autoloaderFiles as $file) {
      AutoloaderHijacker::hijack($file, new AutoloaderWrapper());
    }
  }
  public function hijackAutoloader(string $class): self
  {
    $this->autoloaders[] = $class;
    return $this;
  }
  public function hijackAutoloaderMethod(string $file): self
  {
    $this->autoloaderFiles[] = $file;
    return $this;
  }
  public function getTemporaryDirectory(): string
  {
    return $this->config->getTemporaryFilesDirectory();
  }
  /** @return string[] */
  public function getDirectories(): array
  {
    return $this->config->getDirectories();
  }
  public function shouldUseCache(): bool
  {
    return $this->config->getUseCache();
  }
}
