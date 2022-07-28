<?php

namespace Tests\Support;

class SandboxHelper
{
    public static function storeCodeInTemp(string $code): string {
        $path = sys_get_temp_dir() . "/" . md5($code) . '.php';
        $return = file_put_contents($path, $code);
        if(false === $return) {
            throw new \RuntimeException("Unable to create file for sandbox: $path");
        }
        return $path;
    }
    public static function getCode(\Closure $closure, bool $stripNamespaces): string {
        try {
            $code = (new ReflectionClosure($closure))->getCode();
        } catch (\ReflectionException $e) {
            throw new \RuntimeException("Failure during sandbox setup: " . $e->getMessage());
        }
        // Replace wrapping closure code
        $code = preg_replace('/static function\(.*\).+{/', '', $code);
        // last character should always be the closing brace of a closure
        $code = substr($code, 0, -1);
        if($stripNamespaces) {
            $code = preg_replace('/\\\\.+\\\\/m', '', $code);
        }
        return $code;
    }
}