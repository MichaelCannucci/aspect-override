<?php

namespace AspectOverride\Lexer\Token\Special;

use AspectOverride\Lexer\Sequence;
use AspectOverride\Lexer\Token\TokenMatches;

class MethodSignatureDeclaration implements TokenMatches
{
    public function matches(int $key, string $token, string $normalizedToken): Sequence
    {
        // Should be anything that end with ';'
        $parts = explode(';', $normalizedToken);
        if(count($parts) !== 2 || $normalizedToken[strlen($normalizedToken) - 1] !== ';') {
            return Sequence::FAIL();
        }
        // Shouldn't be a number or start with $ (ex: return $a; or return 2;),
        // if it's not then it's a interface or abstract
        return str_contains("$", $parts[0]) || is_numeric($parts[0]) ? Sequence::FAIL() : Sequence::NEXT();
    }
}