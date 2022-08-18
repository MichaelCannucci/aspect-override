<?php

use AspectOverride\Lexer\OnSequenceMatched;
use AspectOverride\Lexer\SequenceGenerator;
use AspectOverride\Lexer\SequenceRules;
use AspectOverride\Lexer\Token\Capture;
use AspectOverride\Lexer\Token\Token as T;

it("transform tokens with a capture", function() {
    $seq = new SequenceRules([
       new SequenceGenerator([T::capture(T::of("123"))], new class implements OnSequenceMatched {
           public function __invoke(array $captures): array {
               return array_map(function(Capture $capture) {
                   $capture->text = $capture->text . '45';
                   return $capture;
               }, $captures);
           }
       })
    ]);
    expect($seq->transform("echo 123"))->toBe("echo 12345");
});