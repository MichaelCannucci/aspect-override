<?php

namespace AspectOverride\Core;

use AspectOverride\Utility\FilePaths;

class Configuration {
    /** @var string[] */
    protected $directories;

    /** @var string[] */
    protected $excludedDirectories;

    /** @var false|string */
    private $debugDump;

    public static function create(): self {
        return new self();
    }

    public function __construct() {
        $this->directories = [];
        $this->excludedDirectories = [];
        $this->debugDump = false;
    }

    /**
     * @param string[] $directories
     * @return $this
     */
    public function setDirectories(array $directories): self {
        $this->directories = FilePaths::normalizeDirectories($directories);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getDirectories(): array {
        return $this->directories;
    }

    /**
     * @param string[] $directories
     * @return $this
     */
    public function setExcludedDirectories(array $directories): self {
        $this->excludedDirectories = FilePaths::normalizeDirectories($directories);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getExcludedDirectories(): array {
        return $this->excludedDirectories;
    }

    /**
     * @param false|string $debug
     */
    public function setDebugDump($debug): self {
        if($debug && !file_exists($debug)) {
            mkdir($debug, 0777, true);
        }
        $this->debugDump = $debug;
        return $this;
    }

    /**
     * @return false|string
     */
    public function getDebugDump() {
        return $this->debugDump;
    }
}
