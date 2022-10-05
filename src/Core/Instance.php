<?php

namespace AspectOverride\Core;

class Instance {
    /**
     * @var Configuration
     */
    protected $config;
    /**
     * @var StreamInterceptor
     */
    protected $interceptor;
    /**
     * @var ClassRegistry
     */
    protected $classRegistry;
    /**
     * @var FunctionRegistry
     */
    protected $functionRegistry;
    /**
     * @var FileChecker|null
     */
    protected $fileChecker;
    /**
     * @var Execution|null
     */
    protected $execution;

    public function __construct(
        Configuration     $configuration,
        StreamInterceptor $interceptor = null,
        ClassRegistry     $classOverwriteRegistry = null,
        FunctionRegistry  $functionRegistry = null,
        FileChecker       $fileChecker = null,
        Execution         $execution = null
    ) {
        $this->config = $configuration;
        $this->interceptor = $interceptor ?? new StreamInterceptor();
        $this->classRegistry = $classOverwriteRegistry ?? new ClassRegistry();
        $this->functionRegistry = $functionRegistry ?? new FunctionRegistry();
        $this->fileChecker = $fileChecker ?? new FileChecker($this->config);
        $this->execution = $execution ?? new Execution();
        $this->initialize();
    }

    private function initialize(): void {
        $this->interceptor->restore();
        $this->interceptor->intercept();
    }

    public function close(): void {
        $this->interceptor->restore();
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

    public function resetRegistry(): self {
        $this->classRegistry->reset();
        $this->functionRegistry->reset();
        return $this;
    }

    public function wrapAround(string $class, string $method, array $args, callable $execute): array {
        $around = $this->classRegistry->get($class, $method) ?? function (callable $execute, ...$args) {
            return $execute(...$args);
        };
        return $this->execution->wrap($around, $args, $execute);
    }

    public function getForFunction(string $fn): ?callable {
        return $this->functionRegistry->get($fn);
    }

    public function shouldProcess(string $path): bool {
        return $this->fileChecker->shouldProcess($path);
    }
}