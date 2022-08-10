<?php

use AspectOverride\Lexer\OnSequenceMatched;
use AspectOverride\Lexer\SequenceGenerator;
use AspectOverride\Lexer\SequenceMatchingLexer;
use AspectOverride\Lexer\Token\Token as T;

it("transform tokens based on a simple sequence", function() {
    $lexer = new SequenceMatchingLexer([
        new SequenceGenerator([T::of('echo')], new class implements OnSequenceMatched {
            public function __invoke(array $captures) {
                return ' test';
            }
        })
    ]);
    expect($lexer->transform("echo 123"))->toBe("echo test 123");
});