<?php

namespace AspectOverride\Core;

class Instance
{
    /** @var Configuration */
    protected $config;
    /** @var StreamInterceptor */
    protected $interceptor;

    public function __construct(
        StreamInterceptor $interceptor = null
    ) {
        $this->interceptor = $interceptor ?? new StreamInterceptor();
    }

    public function initialize(Configuration $configuration): void
    {
        $this->config = $configuration;
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
}
