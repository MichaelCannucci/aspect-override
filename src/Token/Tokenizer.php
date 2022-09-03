<?php

namespace AspectOverride\Token;

use PhpToken;

class Tokenizer {
    /**
     * @var TokenMachine
     */
    private $machine;

    public function __construct(
        TokenMachine $machine = null
    ) {
        $this->machine = $machine ?? new TokenMachine();
    }

    public function transform(string $code): string {
        $buffer = [];
        $temporary = $this->requiresTemporaryOpenTag($code);
        $tokens = PhpToken::tokenize(($temporary ? "<?php " : "") . $code);
        if ($temporary) {
            array_shift($tokens);
        }
        foreach ($tokens as $index => $token) {
            $buffer[$index] = $this->machine->process($token);
        }
        return implode('', $buffer);
    }

    public function getMachine(): TokenMachine {
        return $this->machine;
    }

    protected function requiresTemporaryOpenTag(string $code): bool {
        return trim(substr($code, 0, 6)) !== "<?php";
    }
}
