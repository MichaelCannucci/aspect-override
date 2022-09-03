<?php

use AspectOverride\Lexer\OnSequenceMatched;
use AspectOverride\Lexer\SequenceGenerator;
use AspectOverride\Lexer\Tokenizer;
use AspectOverride\Lexer\Token\Capture;
use AspectOverride\Lexer\Token\Token as T;
use AspectOverride\Lexer\TokenMachine;

it("transforms code when passing through the state machine", function() {
    $tokenizer = new Tokenizer(new TokenMachine([
        TokenMachine::FUNCTION_START => function(PhpToken $token): string {
            return $token->text . ' START';
        },
        TokenMachine::FUNCTION_END => function(PhpToken $token): string {
            return ' END ' . $token->text;
        }
    ]));
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

it("transforms code if function name is a reserved keyword", function() {
    $tokenizer = new Tokenizer(new TokenMachine([
        TokenMachine::FUNCTION_START => function(PhpToken $token): string {
            return $token->text . ' START';
        },
        TokenMachine::FUNCTION_END => function(PhpToken $token): string {
            return 'END ' . $token->text;
        }
    ]));
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

it("transforms code when even if there is an function declaration", function() {
    $tokenizer = new Tokenizer(new TokenMachine([
        TokenMachine::FUNCTION_START => function(PhpToken $token): string {
            return $token->text . ' START';
        },
        TokenMachine::FUNCTION_END => function(PhpToken $token): string {
            return ' END ' . $token->text;
        }
    ]));
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