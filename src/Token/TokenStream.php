<?php

namespace AspectOverride\Token;

use AspectOverride\Token\Machine\TokenMachineInterface;

class TokenStream {
    /**
     * @var TokenMachineInterface
     */
    protected $machine;
    /**
     * @var Tokenizer
     */
    protected $tokenizer;
    /**
     * @var string
     */
    protected $last = '';

    public function __construct(
        TokenMachineInterface $machine,
        Tokenizer $tokenizer = null
    ) {
        $this->machine = $machine;
        $this->tokenizer = $tokenizer ?? new Tokenizer();
    }

    public function transform(string $code): string {
        /** @var string[] $buffer */
        $buffer = [];
        $code = $this->last . $code;
        foreach ($this->tokenizer->tokens($code) as $index => $token) {
            $buffer[$index] = $this->machine->process($token);
        }
        // Keep the last token in the buffer if it's not a valid end
        // and looks like it's in the middle (we'll append it in the next buffer)
        $last = end($buffer);
        if (is_string($last)) {
            if (!in_array(trim($last), ['}', ';', ''])) {
                $this->last = array_pop($buffer) ?? '';
            } else {
                $this->last = '';
            }
        }
        return implode('', $buffer);
    }

    public function reset(): void {
        $this->last = '';
        $this->machine->reset();
    }
}
