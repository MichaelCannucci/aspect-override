<?php

namespace Tests\Support;

/** Hacky code to get tests running */
class SandboxHelper {
    public static function generateRunner(\Closure $setup, string $injectedPath): string {
        $directory = dirname($injectedPath);
        $cwd = getcwd();
        $setupCode = self::getCode($setup);
        $runner = /** @lang PHP */ "<?php
            require '$cwd/vendor/autoload.php';
            
            AspectOverride\Facades\Instance::initialize(
            AspectOverride\Core\Configuration::create()
                ->setDirectories([ '$directory' ])
            );
        
            require '$setupCode';
                        
            require '$injectedPath';
        ";
        return self::tempFile($runner);
    }
    public static function getCode(\Closure $closure, bool $stripNamespaces = false): string {
        try {
            $fn = new \ReflectionFunction($closure);
            $start = $fn->getStartLine();
            $end = $fn->getEndLine();
            $file = file_get_contents($fn->getFileName());
            // copying namespaces from the file is hacky but it works for now :(
            preg_match_all('/use.+/', $file, $matches);
            $namespaces = array_map(function ($m) {
                return $m[0];
            }, $matches);
            $code = "";
            $lineNum = 0;
            for ($line = strtok($file, "\n"), 0; $line !== false; $line = strtok("\n")) {
                $lineNum++;
                if ($lineNum === $start) {
                    $line = preg_replace('/static function\(.*\).+{/', '', $line);
                }
                if ($lineNum === $end) {
                    $line = preg_replace('/}?,?/', '', $line);
                }
                if ($lineNum >= $start && $lineNum <= $end) {
                    $code .= $line . "\n";
                }
            }
            if ($stripNamespaces) {
                $code = preg_replace('/\\\\.+\\\\/m', '', $code);
            }
            return self::tempFile("<?php" . PHP_EOL . implode("\n", $namespaces) . PHP_EOL . $code);
        } catch (\ReflectionException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
    public static function tempFile(string $code): string {
        $tmp = $_ENV['TEST_RUNNER_TMP'] ?? sys_get_temp_dir();
        $path = $tmp . "/" . md5($code) . '.php';
        $return = file_put_contents($path, $code);
        if (false === $return) {
            throw new \RuntimeException("Unable to create file for sandbox: $path");
        }
        return $path;
    }
}
