<?php

namespace AspectOverride\Core;

class Registry
{
    /** @var array<string,array<string,callable>> */
    protected $classMap = [];
    /** @var array<string,callable> */
    protected $fnMap = [];

    /** @param class-string $class */
    public function setForClass(string $class, string $method, callable $fn): void
    {
        $this->classMap[$class][$method] = $fn;
    }

    /** @param class-string $class */
    public function getForClass(string $class, string $method): ?callable
    {
        return $this->classMap[$class][$method] ?? null;
    }

    public function removeForClass(string $class, string $method): void
    {
        unset($this->classMap[$class][$method]);
    }

    public function setForFunction(string $fnName, callable $fn): void
    {
        $this->fnMap[$fnName] = $fn;
    }

    public function getForFunction(string $fn): ?callable
    {
        return $this->fnMap[$fn] ?? null;
    }

    /** @return string[] */
    public function getFunctions(): array
    {
        return array_keys($this->fnMap);
    }

    public function removeForFunction(string $fn): void
    {
        unset($this->fnMap[$fn]);
    }

    public function clean(): void
    {
        $this->classMap = [];
        $this->fnMap = [];
    }
}