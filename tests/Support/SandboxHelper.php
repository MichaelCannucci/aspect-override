<?php

namespace Tests\Support;

class SandboxHelper {

    public static function storeCode(string $path, string $code) {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $path = $path . "/" . md5($code) . '.php';
        $return = file_put_contents($path, $code);
        if (false === $return) {
            throw new \RuntimeException("Unable to create file for sandbox: $path");
        }
        return $path;
    }

    public static function getCode(\Closure $closure, bool $stripNamespaces = false): string {
        try {
            $fn = new \ReflectionFunction($closure);
            $start = $fn->getStartLine();
            $end = $fn->getEndLine();
            $file = file_get_contents($fn->getFileName());
            // copying namespaces from the file is hacky, but it works for now :(
            preg_match_all('/use.+/', $file, $matches);
            $namespaces = array_map(function ($m) {
                return $m[0];
            }, $matches);
            $code = "";
            $lines = file($fn->getFileName());
            for($l = $start; $l < $end; $l++) {
                if ($l === $start) {
                    $code .= preg_replace('/static function\(.*\).+{/', '', $lines[$l]);
                } else if ($l === $end - 1) {
                    $code .= preg_replace('/}?,?/', '', $lines[$l]);
                } else {
                    $code .= $lines[$l];
                }
            }
            if ($stripNamespaces) {
                $code = preg_replace('/\\\\.+\\\\/m', '', $code);
            }
            return "<?php" . PHP_EOL . implode("\n", $namespaces) . PHP_EOL . $code;
        } catch (\ReflectionException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
