<?php

namespace AspectOverride\Core;

class Instance
{
    /** @var Configuration */
    protected $config;
    /** @var StreamInterceptor */
    protected $interceptor;
    /** @var Registry */
    protected $registry;

    public function __construct(
        Configuration $configuration = null,
        StreamInterceptor $interceptor = null,
        Registry $registry = null,
    ) {
        $this->interceptor = $interceptor ?? new StreamInterceptor($configuration);
        $this->config = $configuration ?? new Configuration();
        $this->registry = $registry ?? new Registry();
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

    public function getRegistry(): Registry
    {
        return $this->registry;
    }
}
