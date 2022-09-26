<?php

namespace Tests\Support;

class TestHelper {

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
            return "<?php" . PHP_EOL . $code;
        } catch (\ReflectionException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
