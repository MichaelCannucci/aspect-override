<?php

namespace AspectOverride\Utility;

class FilePaths
{
    /**
     *
     * @param string $path
     * @return string
     */
    public static function almostRealPath(string $path): string {
        $paths = explode(DIRECTORY_SEPARATOR, $path);
        $length = count($paths);
        for($i = 0; $i < $length; $i++) {
            $item = $paths[$i];
            if($item === '..' && isset($paths[$i++])) {
                unset($paths[$i++]);
                $i++;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $paths);
    }
}