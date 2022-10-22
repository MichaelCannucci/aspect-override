<?php

namespace AspectOverride\Processors;

interface CodeProcessorInterface {
    public function transform(string $data): string;

    public function onNew(): void;
}
