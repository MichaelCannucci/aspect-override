<?php

namespace Tests\Support;

class SandboxHelper
{
    public static function generateRunner(\Closure $setup, string $injectedPath): string {
        $directory = dirname($injectedPath);
        $cwd = getcwd();
        $setupCode = self::getCode($setup);
        $runner = /** @lang InjectablePHP */ "<?php
            require '$cwd/vendor/autoload.php';
            
            AspectOverride\Facades\Instance::initialize(
            AspectOverride\Core\Configuration::create()
                ->setDirectories([ '$directory' ])
            );
        
            require '$setupCode';
                        
            require '$injectedPath';
        ";
        return SandboxHelper::tempFile($runner);
    }
    public static function getCode(\Closure $closure, bool $stripNamespaces = false): string {
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
        $code = "<?php" . PHP_EOL . $code;
        return self::tempFile($code);
    }
    private static function tempFile(string $code): string {
        $path = sys_get_temp_dir() . "/" . md5($code) . '.php';
        $return = file_put_contents($path, $code);
        if(false === $return) {
            throw new \RuntimeException("Unable to create file for sandbox: $path");
        }
        return $path;
    }
}