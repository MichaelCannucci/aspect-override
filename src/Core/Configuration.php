<?php

namespace AspectOverride\Core;

class Configuration
{
    /** @var string[] */
    protected $directories;

    /** @var bool */
    protected $debug;

    public static function create(): self
    {
        return new self();
    }

    public function __construct()
    {
        $this->directories = [];
        $this->debug = false;
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

    /**
     * @return string[]
     */
    public function getDirectories(): array
    {
        return $this->directories;
    }

    public function setDebugMode(bool $status): self
    {
        $this->debug = $status;
        return $this;
    }

    public function isInDebugMode(): bool
    {
        return $this->debug;
    }
}
