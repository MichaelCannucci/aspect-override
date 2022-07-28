<?php

namespace AspectOverride\Core;

class FunctionRegistry
{
    /** 
     * @var array<string,callable> 
     **/
    protected $fnMap = [];

    /**
     * Set the callable to use for global functions
     * @param string $fnName 
     * @param callable $fn 
     * @return void 
     */
    public function set(string $fnName, callable $fn): void
    {
        $this->fnMap[$fnName] = $fn;
    }

    /**
     * Get the callable which overrides a global function
     * @return null|callable if there are no overrides for this function nothing is returned
     */
    public function get(string $fn): ?callable
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
    public function remove(string $fn): void
    {
        unset($this->fnMap[$fn]);
    }

    /**
     * Remove all overrides
     */
    public function reset(): void
    {
        $this->fnMap = [];
    }
}
