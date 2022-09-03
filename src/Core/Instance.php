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
        $this->interceptor = $interceptor ?? new StreamInterceptor($configuration);
        $this->config = $configuration ?? new Configuration();
        $this->classRegistry = $classOverwriteRegistry ?? new ClassRegistry();
        $this->functionRegistry = $functionRegistry ?? new FunctionRegistry();
        $this->reset();
        $this->start();
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

    public function resetRegistry(): self {
        $this->classRegistry->reset();
        $this->functionRegistry->reset();
        return $this;
    }
}
