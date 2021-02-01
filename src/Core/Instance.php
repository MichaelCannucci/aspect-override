<?php

namespace AspectOverride\Core;

use AspectOverride\Loader\AutoloaderHijacker;
use AspectOverride\Loader\AutoloaderWrapper;
use AspectOverride\Util\Configuration;
use Composer\Autoload\ClassLoader;
use ReflectionClass;
use RuntimeException;

class Instance
{
  /** @var Configuration */
  protected $config;
  /** @var array<string,callable> */
  protected $autoloaderFiles = [];
  /** @var ?self */
  protected static $instance;
  /** @var bool */
  protected $autoLoaderNotConfigured = true;

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
    // Fill the missing parameter keys (PHP 8.0, I miss you)
    $reflection = new ReflectionClass(Configuration::class);
    $constructor = $reflection->getConstructor();
    if(null === $constructor) {
      throw new RuntimeException("Configuration::class doesn't have a constructor?");
    }
    foreach ($constructor->getParameters() as $parameter) {
      if(!array_key_exists($parameter->getName(), $options)) {
        $options[$parameter->getName()] = $parameter->getDefaultValue();
      }
    }
    ksort($options);
    $this->config = new Configuration(...array_values($options));
    if($this->autoLoaderNotConfigured) {
      AutoloaderHijacker::hijack(ClassLoader::class, new AutoloaderWrapper());
      foreach ($this->autoloaderFiles as $file => $fileResolver) {
        AutoloaderHijacker::hijackMethod($file, $fileResolver, new AutoloaderWrapper());
      }
      $this->autoLoaderNotConfigured = false;
    }
  }
  /**
   * @param string $file function's file location
   * @param callable $fileResolver find files for original function
   * @return self
   */
  public function hijackAutoloaderMethod(string $file, callable $fileResolver): self
  {
    $this->autoloaderFiles[$file] = $fileResolver;
    return $this;
  }
  public function getConfiguration(): Configuration
  {
    return $this->config;
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
