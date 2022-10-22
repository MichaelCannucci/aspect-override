<?php

namespace AspectOverride\Core;

class Execution {
    /**
     * @param mixed[] $args
     * @return mixed[]
     */
    public function wrap(callable $callable, array $args, callable $execute): array {
        // temporary holder for arguments while we mutate them
        $tArgs = array_values($args);
        $result = $callable($execute, ...$tArgs);
        // we need the original argument names back for the 'extract' method to apply the arguments back
        return [array_combine(array_keys($args), $tArgs), $result];
    }
}
