<?php

namespace AspectOverride;

use AspectOverride\Core\Instance;

class Builder
{
    private $paths = [];
    public static function create(): self
    {
        return new self();
    }
    /** @param string[] $paths */
    public function setAllowedDirectories(array $paths): self
    {
        $this->paths = $paths;
        return $this;
    }
    public function load(): Instance
    {
        \AspectOverride\Facades\Instance::initialize(
            \AspectOverride\Core\Configuration::create()
                ->setDirectories($this->paths)
        );
        return \AspectOverride\Facades\Instance::$instance;
    }
}
