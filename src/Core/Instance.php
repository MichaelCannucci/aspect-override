<?php

namespace AspectOverride\Core;

class Instance
{
    /** @var Configuration */
    protected $config;
    /** @var StreamInterceptor */
    protected $interceptor;
    /** @var ClassRegistry */
    protected $classOverwriteRegistry;
    /** @var ClassRegistry */
    protected $classBeforeRegistry;
    /** @var ClassRegistry */
    protected $classAfterRegistry;
    /** @var FunctionRegistry */
    protected $functionRegistry;

    public function __construct(
        Configuration     $configuration = null,
        StreamInterceptor $interceptor = null,
        ClassRegistry     $classOverwriteRegistry = null,
        ClassRegistry     $classBeforeRegistry = null,
        ClassRegistry     $classAfterRegistry = null,
        FunctionRegistry  $functionRegistry = null
    ) {
        $this->interceptor = $interceptor ?? new StreamInterceptor($configuration);
        $this->config = $configuration ?? new Configuration();
        $this->classBeforeRegistry = $classBeforeRegistry ?? new ClassRegistry();
        $this->classAfterRegistry = $classAfterRegistry ?? new ClassRegistry();
        $this->classOverwriteRegistry = $classOverwriteRegistry ?? new ClassRegistry();
        $this->functionRegistry = $functionRegistry ?? new FunctionRegistry();
        $this->reset();
        $this->start();
    }

    public function reset(): void
    {
        $this->interceptor->restore();
    }

    public function start(): void
    {
        $this->interceptor->intercept();
    }

    public function getConfiguration(): Configuration
    {
        return $this->config;
    }

    public function getClassOverwriteRegistry(): ClassRegistry
    {
        return $this->classOverwriteRegistry;
    }

    public function getClassBeforeRegistry(): ClassRegistry
    {
        return $this->classBeforeRegistry;
    }

    public function getClassAfterRegistry(): ClassRegistry
    {
        return $this->classAfterRegistry;
    }

    public function getFunctionRegistry(): FunctionRegistry
    {
        return $this->functionRegistry;
    }

    public function resetRegistry(): self {
        $this->classOverwriteRegistry->reset();
        $this->classBeforeRegistry->reset();
        $this->classAfterRegistry->reset();
        $this->functionRegistry->reset();
        return $this;
    }
}
