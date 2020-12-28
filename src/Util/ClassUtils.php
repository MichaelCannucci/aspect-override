<?php

namespace AspectOverride\Util;

use AspectOverride\Exceptions\MalformedNamespacedItem;

class ClassUtils
{
  public static function getNamespace(string $classString): string
  {
    // Remove the last segment on the FQN (ex: MyTest\A\B\ActualClassName -> MyTest\A\B)
    return
    implode('\\',
      array_slice(
        explode('\\', str_replace('\\\\', '\\', $classString)),
        0,
        -1
      )
    );
  }
}