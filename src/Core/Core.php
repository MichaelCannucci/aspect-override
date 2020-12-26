<?php

namespace AspectOverride\Core;

use AspectOverride\Loader\AutoloaderHijacker;
use AspectOverride\Loader\AutoloaderWrapper;
use Composer\Autoload\ClassLoader;
use RuntimeException;

class Core
{
  /** @var string[] */
  protected $directories  = [];
  /** @var bool */
  protected $hasBeenInitialized = false;
  /** @var string */
  protected $temporaryFilesDir = '/tmp/aspect_override';
  /** @var bool */
  protected $useCache = false;
  /** @var self */
  protected static $instance;

  protected function __construct() { }
  public static function getInstance(): self
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  /** @param array{
   * directories: string[],
   * ?temporaryFilesDir: string,
   * ?useCache: bool
   * } $options */
  public function init(array $options)
  {
    if($this->hasBeenInitialized) {
      return;
    }
    if(isset($options['temporaryFilesDir'])) {
      $tmp = $options['temporaryFilesDir'];
      // Ensure that the temporary directory specified always has an ending slash
      if($tmp[strlen($tmp)] !== '/') {
        $tmp .= '/';
      }
      $this->temporaryFilesDir = $tmp;
    }
    if(!isset($options['directories'])) {
      throw new RuntimeException("Expecting directories to be listed!");
    }
    $this->useCache = $options['useCache'] ?? false;
    $this->directories = $this->normalizeDirectories($options['directories']);
    AutoloaderHijacker::hijack(ClassLoader::class, new AutoloaderWrapper());
    $this->hasBeenInitialized = true;
  }
  public function getTemporaryDirectory()
  {
    return $this->temporaryFilesDir;
  }
  public function getDirectories()
  {
    return $this->directories;
  }
  public function shouldUseCache()
  {
    return $this->useCache;
  }
  /** @param string[] $directories */
  protected function normalizeDirectories(array $directories)
  {
    return array_filter(array_map(
      function (string $directory) {
        return realpath($directory);
      },
      $directories
    ));
  }
}
