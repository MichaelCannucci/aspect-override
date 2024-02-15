<?php

use AspectOverride\Token\Machine\ClassTokenMachine;
use AspectOverride\Token\Tokenizer;
use AspectOverride\Token\TokenStream;

const CODE = '<?php class Test { public function test($a, int $b) { return 1; } }';

it('can transform if split in the middle of the file', function ($substring) {
    expect($substring)
        ->not()
        ->toBeFalse()
        ->and(strpos(CODE, $substring))
        ->toBe(0); // should be the start of the string
    $pos = strlen($substring);
    $stream = new TokenStream(new ClassTokenMachine([
        ClassTokenMachine::FUNCTION_START => function(PhpToken $token) { return 'START ' . $token->text; },
        ClassTokenMachine::FUNCTION_END   => function(PhpToken $token) { return ' END '  . $token->text; }
    ]), new Tokenizer());
    $transformed = "";
    foreach (explode("\r\n", chunk_split(CODE, $pos)) as $chunk) {
        $transformed .= $stream->transform($chunk);
    }
    expect($transformed)->toContain("START", "END");
})->with([
    'in the middle of function' => '<?php class Test { public func',
    'in the middle of function name' => '<?php class Test { public function te',
    'in the middle of a functions arguments' => '<?php class Test { public function test($a, int ',
    // Other keywords shouldn't matter since they're one character
]);