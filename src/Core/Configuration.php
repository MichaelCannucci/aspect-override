<?php

namespace AspectOverride\Core;

class Configuration
{
    /** @var string[] */
    protected $directories;
    /** @var string */
    protected $tempFilesDir;
    /** @var bool */
    protected $useCaching;

    public static function create(): self
    {
        return new self();
    }

    public function __construct()
    {
        $this->directories = [];
        $this->tempFilesDir = $this->setTemporaryFilesDirectory('/tmp/aspect-override');
    }

    /** @return array<string,string|string[]|bool> */
    public function getRaw()
    {
        return [
            'directories' => $this->directories,
            'tempFilesDir' => $this->tempFilesDir
        ];
    }

    /**
     * @param string[] $directories
     * @return $this
     */
    public function setDirectories(array $directories): self
    {
        $this->directories = array_filter(
            array_map(function (string $directory) {
                return realpath($directory);
            }, $directories)
        );
        return $this;
    }

    public function setTemporaryFilesDirectory(string $directory): self
    {
        // Ensure that the temporary directory specified always has an ending slash
        if ($directory[strlen($directory) - 1] !== '/') {
            $directory .= '/';
        }
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $this->tempFilesDir = $directory;
        return $this;
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
}