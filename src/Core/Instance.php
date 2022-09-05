<?php

namespace AspectOverride\Core;

class Instance {
    /** @var Configuration */
    protected $config;
    /** @var StreamInterceptor */
    protected $interceptor;
    /** @var ClassRegistry */
    protected $classRegistry;
    /** @var FunctionRegistry */
    protected $functionRegistry;

    public function __construct(
        Configuration     $configuration = null,
        StreamInterceptor $interceptor = null,
        ClassRegistry     $classOverwriteRegistry = null,
        FunctionRegistry  $functionRegistry = null
    ) {
        $this->interceptor = $interceptor ?? new StreamInterceptor();
        $this->config = $configuration ?? new Configuration();
        $this->classRegistry = $classOverwriteRegistry ?? new ClassRegistry();
        $this->functionRegistry = $functionRegistry ?? new FunctionRegistry();
    }

    public function reset(): void {
        $this->interceptor->restore();
    }

    public function start(): void {
        $this->interceptor->intercept();
    }

    public function getConfiguration(): Configuration {
        return $this->config;
    }

    public function getClassRegistry(): ClassRegistry {
        return $this->classRegistry;
    }

    public function getFunctionRegistry(): FunctionRegistry {
        return $this->functionRegistry;
    }

    public function getStreamInterceptor(): StreamInterceptor {
        return $this->interceptor;
    }

    public function resetRegistry(): self {
        $this->classRegistry->reset();
        $this->functionRegistry->reset();
        return $this;
    }

    public function dump($data): void {
        if($path = \AspectOverride\Facades\Instance::getConfiguration()->getDebugDump()) {
            $name = md5($data);
            file_put_contents("$path/$name.php", $data);
        }
    }

    public function getForFunction(string $fn): ?callable {
        return $this->getFunctionRegistry()->get($fn);
    }

    /**
     * @param class-string $class
     * @param string $method
     * @param mixed[] $args
     * @param callable $execute
     * @return mixed
     */
    public function wrapAround(string $class, string $method, array $args, callable $execute): array {
        $stub = function (callable $execute, ...$args) {
            return $execute(...$args);
        };
        $around = $this->getClassRegistry()->get($class, $method) ?? $stub;
        // temporary holder for arguments while we mutate them
        $tArgs = array_values($args);
        $result = $around($execute, ...$tArgs);
        // we need the original argument names back for the 'extract' method to apply the arguments back
        return [array_combine(array_keys($args), $tArgs), $result];
    }

    public function shouldProcess(string $uri): bool {
        $uri = realpath($uri);
        $excludedDirectories = \AspectOverride\Facades\Instance::getConfiguration()->getExcludedDirectories();
        foreach ($excludedDirectories as $excluded) {
            if ($this->isPhpFile($uri) && false !== strpos($uri, $excluded)) {
                return false;
            }
        }
        $allowedDirectories = \AspectOverride\Facades\Instance::getConfiguration()->getDirectories();
        foreach ($allowedDirectories as $directory) {
            if ($this->isPhpFile($uri) && false !== strpos($uri, $directory)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determines that the provided uri leads to a PHP file.
     */
    private function isPhpFile(string $uri): bool {
        return 'php' === pathinfo($uri, PATHINFO_EXTENSION);
    }
}
