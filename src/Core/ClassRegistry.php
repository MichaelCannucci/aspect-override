<?php

namespace AspectOverride\Core;

class ClassRegistry
{
    /** 
     * @var array<string,array<string,callable>> 
     **/
    protected $classMap = [];

    /** 
     * Set the method override for a specific class
     * @psalm-param class-string $class 
     **/
    public function set(string $class, string $method, callable $fn): void
    {
        $this->classMap[$class][$method] = $fn;
    }

    /** 
     * Get the callable that should be used for the class
     * @psalm-param class-string $class 
     * @return callable|null If there are no callable for this method null is returned
     **/
    public function get(string $class, string $method): ?callable
    {
        return $this->classMap[$class][$method] ?? null;
    }

    /**
     * Remove a method override for a class
     * @psalm-param class-string $class 
     */
    public function remove(string $class, string $method): void
    {
        unset($this->classMap[$class][$method]);
    }

    /**
     * Remove all overrides
     */
    public function reset(): void
    {
        $this->classMap = [];
    }
}
