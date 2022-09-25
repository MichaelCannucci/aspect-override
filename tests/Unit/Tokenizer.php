<?php

use AspectOverride\Token\TokenStream;
use AspectOverride\Token\TokenMachine;

$tokenizer = new TokenStream(new TokenMachine([
    TokenMachine::FUNCTION_START => function(PhpToken $token): string {
        return $token->text . ' START';
    },
    TokenMachine::FUNCTION_END => function(PhpToken $token): string {
        return 'END ' . $token->text;
    }
]));

it("transforms code when passing through the state machine", function() use ($tokenizer) {
    expect($tokenizer->transform("
    class Test {
        public function testing() {}
    }
    "))->toBe("
    class Test {
        public function testing() { START END }
    }
    ");
});

it("transforms code if function name is a reserved keyword", function() use ($tokenizer) {
    expect($tokenizer->transform("
    class Test {
        public static function empty() {
        }
    }
    "))->toBe("
    class Test {
        public static function empty() { START
        END }
    }
    ");
});

it("transforms code when even if there is an function declaration", function() use ($tokenizer) {
    expect($tokenizer->transform("
    class Test {
        public function shouldDoSomething(\$a, int \$b) {}
        public abstract function test(\$a);
    }
    interface TestInterface {
        function test();
    }
    "))->toBe("
    class Test {
        public function shouldDoSomething(\$a, int \$b) { START END }
        public abstract function test(\$a);
    }
    interface TestInterface {
        function test();
    }
    ");
});