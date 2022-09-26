<?php

namespace AspectOverride\Token;

use PhpToken;

class Tokenizer
{
    /**
     * @return PhpToken[]
     */
    public function tokens(string $code): array {
        $temporary = $this->requiresTemporaryOpenTag($code);
        $tokens = PhpToken::tokenize(($temporary ? "<?php " : "") . $code);
        if ($temporary) {
            array_shift($tokens);
        }
        return $tokens;
    }

    protected function requiresTemporaryOpenTag(string $code): bool {
        return trim(substr($code, 0, 6)) !== "<?php";
    }
}