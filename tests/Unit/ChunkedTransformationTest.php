<?php

use AspectOverride\Token\Tokenizer;
use AspectOverride\Token\TokenMachine;
use AspectOverride\Token\TokenStream;

const TEST_CODE = "<?php class Test { public function test() { return 1; } }";

function transformCodeInChunks(string $code, int $length): string {
    $stream = new TokenStream(new TokenMachine([
        TokenMachine::FUNCTION_START => 'START ',
        TokenMachine::FUNCTION_END   => ' END '
    ]), new Tokenizer());
    $transformed = "";
    foreach (explode("\r\n", chunk_split($code, $length)) as $chunk) {
        $transformed .= $stream->transform($chunk);
    }
    return $transformed;
}

it('can transform if split on function name', function() {
    expect(TEST_CODE)->not()->toBe(transformCodeInChunks(TEST_CODE, 37));
});

it("can transform if split on function keyword", function() {
    expect(TEST_CODE)->not()->toBe(transformCodeInChunks(TEST_CODE, 30));
});