<?php

namespace AspectOverride\Util;

class Configuration
{
  /** @var string[] */
  protected $directories;
  /** @var string */
  protected $tempFilesDir;
  /** @var bool */
  protected $disableCaching;

  /** @param string[] $directories */
  public function __construct(
    array $directories = [], 
    string $temporaryFilesDir = '/tmp/aspect-override/', 
    bool $disableCaching = false
  ) {
    $this->directories    = $this->processFolders($directories);
    $this->tempFilesDir   = $this->processTemporary($temporaryFilesDir);
    $this->disableCaching = $disableCaching;
  }
  /** 
   * @param string[] $directories 
   * @return string[]
   */
  protected function processFolders(array $directories): array
  {
    return array_filter(
      array_map(function (string $directory) {return realpath($directory);}, $directories)
    );
  }
  protected function processTemporary(string $dir): string
  {
    // Ensure that the temporary directory specified always has an ending slash
    if ($dir[strlen($dir) - 1] !== '/') {
      $dir .= '/';
    }
    if (!is_dir($dir)) {
      mkdir($dir, 0777, true);
    }
    return $dir;
  }
  /** @return string[] */
  public function getDirectories(): array
  {
    return $this->directories;
  }
  public function getTemporaryFilesDirectory(): string
  {
    return $this->tempFilesDir;
  }
  public function getUseCache(): bool
  {
    return $this->disableCaching;
  }
}