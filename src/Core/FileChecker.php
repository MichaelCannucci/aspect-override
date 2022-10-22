<?php

namespace AspectOverride\Core;

use AspectOverride\Utility\FilePaths;

class FileChecker {
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        Configuration $configuration
    ) {
        $this->configuration = $configuration;
    }

    public function shouldProcess(string $path): bool {
        $path = FilePaths::almostRealPath($path);
        if ($this->isInDirectories($this->configuration->getExcludedDirectories(), $path)) {
            return false;
        }
        return $this->isInDirectories($this->configuration->getDirectories(), $path);
    }

    /**
     * @param string[] $paths
     */
    protected function isInDirectories(array $paths, string $path): bool {
        foreach ($paths as $directory) {
            if ($this->isPhpFile($path) && false !== str_starts_with($path, $directory)) {
                return true;
            }
        }
        return false;
    }

    private function isPhpFile(string $uri): bool {
        return 'php' === pathinfo($uri, PATHINFO_EXTENSION);
    }
}
