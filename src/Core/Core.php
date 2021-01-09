<?php

namespace AspectOverride\Core;

use AspectOverride\Loader\AutoloaderHijacker;
use AspectOverride\Loader\AutoloaderWrapper;
use AspectOverride\Util\Configuration;
use Composer\Autoload\ClassLoader;
use RuntimeException;

class Core
{
  /** @var Configuration */
  protected $config;
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
    AutoloaderHijacker::hijack(ClassLoader::class, new AutoloaderWrapper());
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
