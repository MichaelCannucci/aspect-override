<?php

namespace AspectOverride\Core;

use AspectOverride\Loader\AutoloaderHijacker;
use AspectOverride\Loader\AutoloaderWrapper;
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
    $this->fillDefaultOptions($options);
    $this->config = new Configuration(
      $options['directories'], 
      $options['temporaryFilesDir'],
      $options['disableCaching'],
      $options['hijackMethod']
    );
    if($this->autoLoaderNotConfigured) {
      if($this->config->getHijackMethod() === Configuration::HIJACK_WITH_STREAMS) {
        
      } else {
        AutoloaderHijacker::hijackComposer(new AutoloaderWrapper());
        foreach ($this->autoloaderFiles as $file => $fileResolver) {
          AutoloaderHijacker::hijackMethod($file, $fileResolver, new AutoloaderWrapper());
        }
        $this->autoLoaderNotConfigured = false;
      }
    }
  }
  /** @param array<string,mixed> $options*/
  protected function fillDefaultOptions(array &$options): void
  {
    // Fill the missing parameter keys
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
