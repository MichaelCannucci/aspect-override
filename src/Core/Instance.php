<?php

namespace AspectOverride\Core;

use AspectOverride\Loader\AutoloaderHijacker;
use AspectOverride\Loader\AutoloaderWrapper;
use AspectOverride\Loader\StreamLoader;
use AspectOverride\Util\Configuration;
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
   *  disableCaching: bool,
   *  hijackMethod: 'streams'|'rewrite'
   * } $options 
   */
  public function init(array $options): void
  {
    $this->config = new Configuration(...Configuration::toArgsList($options));
    if($this->autoLoaderNotConfigured) {
      AutoloaderHijacker::hijackComposer(new AutoloaderWrapper());
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
}
