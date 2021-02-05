<?php

namespace AspectOverride\Util;

class Configuration
{
  public const HIJACK_WITH_REWRITE = 'rewrite';
  public const HIJACK_WITH_STREAMS = 'streams';

  /** @var string[] */
  protected $directories;
  /** @var string */
  protected $tempFilesDir;
  /** @var bool */
  protected $disableCaching;
  /** @var string */
  protected $hijackMethod;
  /** 
   * NOTE: constructor arguments have to be sorted name-wise
   * @param string[] $directories 
   */
  public function __construct(
    array $directories = [], 
    bool $disableCaching = false,
    string $temporaryFilesDir = '/tmp/aspect-override/',
    string $hijackMethod = self::HIJACK_WITH_STREAMS
  ) {
    $this->directories       = $this->processFolders($directories);
    $this->tempFilesDir      = $this->processTemporary($temporaryFilesDir);
    $this->disableCaching    = $disableCaching;
    if(!in_array($hijackMethod, [self::HIJACK_WITH_REWRITE, self::HIJACK_WITH_STREAMS])) {
      throw new \RuntimeException("Invalid Value passed in for hijackMethod");
    }
    $this->hijackMethod      = $hijackMethod;
  }
  /** @return array<string,string|string[]|bool> */
  public function getRaw()
  {
    return [
      'directories'    => $this->directories,
      'tempFilesDir'   => $this->tempFilesDir,
      'disableCaching' => $this->disableCaching
    ];
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
    return !$this->disableCaching;
  }
  public function getHijackMethod(): string
  {
    return $this->hijackMethod;
  }
}