<?php

namespace AspectOverride\Core;

class Configuration
{
    /** @var string[] */
    protected $directories;

    /** @var string[] */
    protected $excludedDirectories;

    public static function create(): self
    {
        return new self();
    }

    public function __construct()
    {
        $this->directories = [];
    }

    protected function normalizeDirectories(array $directories) {
        return array_filter(
            array_map(function (string $directory) {
                return realpath($directory);
            }, $directories)
        );
    }

    /**
     * @param string[] $directories
     * @return $this
     */
    public function setDirectories(array $directories): self
    {
        $this->directories = $this->normalizeDirectories($directories);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getDirectories(): array
    {
        return $this->directories;
    }

    /**
     * @param string[] $directories
     * @return $this
     */
    public function setExcludedDirectories(array $directories): self
    {
        $this->excludedDirectories = $this->normalizeDirectories($directories);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getExcludedDirectories(): array
    {
        return $this->excludedDirectories;
    }
}
