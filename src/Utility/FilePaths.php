<?php

namespace AspectOverride\Utility;

class FilePaths {
    /**
     * @param string[] $directories
     * @return string[]
     */
    public static function normalizeDirectories(array $directories): array {
        return array_filter(
            array_map(function (string $directory) {
                return self::almostRealPath($directory);
            }, $directories)
        );
    }

    /**
     *
     * @param string $path
     * @return string
     */
    public static function almostRealPath(string $path): string {
        $paths = explode(DIRECTORY_SEPARATOR, str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path));
        $absolute = [];
        foreach ($paths as $item) {
            switch ($item) {
                case ".":
                    break;
                case "..":
                    array_pop($absolute);
                    break;
                default:
                    $absolute[] = $item;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolute);
    }
}
