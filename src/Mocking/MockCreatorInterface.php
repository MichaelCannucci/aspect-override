<?php

namespace AspectOverride\Mocking;

interface MockCreatorInterface
{
  public function loadMocked(string $originalFilePath): void;
}