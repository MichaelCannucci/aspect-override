<?php

namespace AspectOverride\Core;

class Registry
{
    /** 
     * @var array<string,array<string,callable>> 
     **/
    protected $classMap = [];
    /** 
     * @var array<string,callable> 
     **/
    protected $fnMap = [];

    /** 
     * Set the method override for a specific class
     * @psalm-param class-string $class 
     **/
    public function setForClass(string $class, string $method, callable $fn): void
    {
        $this->classMap[$class][$method] = $fn;
    }

    /** 
     * Get the callable that should be used for the class
     * @psalm-param class-string $class 
     * @return callable|null If there are no callable for this method null is returned
     **/
    public function getForClass(string $class, string $method): ?callable
    {
        return $this->classMap[$class][$method] ?? null;
    }

    /**
     * Remove a method override for a class
     * @psalm-param class-string $class 
     */
    public function removeForClass(string $class, string $method): void
    {
        unset($this->classMap[$class][$method]);
    }

    /**
     * Set the callable to use for global functions
     * @param string $fnName 
     * @param callable $fn 
     * @return void 
     */
    public function setForFunction(string $fnName, callable $fn): void
    {
        $this->fnMap[$fnName] = $fn;
    }

    /**
     * Get the callable which overrides a global function
     * @return null|callable if there are no overrides for this function nothing is returned
     */
    public function getForFunction(string $fn): ?callable
    {
        return $this->fnMap[$fn] ?? null;
    }

    /** 
     * Get a list of functions names that are being overrided
     * @return string[] 
     */
    public function getFunctions(): array
    {
        return array_keys($this->fnMap);
    }

    /**
     * Remove a function that is being overridden
     */
    public function removeForFunction(string $fn): void
    {
        unset($this->fnMap[$fn]);
    }

    /**
     * Remove all overrides
     */
    public function reset(): void
    {
        $this->classMap = [];
        $this->fnMap = [];
    }
}
