<?php

namespace AspectOverride\Mocking;

class ScopedTracker
{
  /** @var string $fn */
  protected $fn;
  public function __construct(string $fn)
  {
    $this->fn = $fn;
  }
  public function __destruct()
  {
    FunctionMocker::unsubscribeToLoading($this->fn);
  }
}