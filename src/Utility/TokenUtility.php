<?php

namespace AspectOverride\Utility;

use AspectOverride\Lexer\Token\Capture;

class TokenUtility
{
    /**
     * @param Capture[] $captures
     */
    public static function wrapCaptures(string $start, string $end, array &$captures): void {
        $beginning = $captures[0];
        $beginning->text = $start . $beginning->text;
        $captures[0] = $beginning;

        $lastIndex = count($captures) - 1;
        $last = $captures[$lastIndex];
        $last->text = $last->text . $end;
        $captures[$lastIndex] = $last;
    }
}