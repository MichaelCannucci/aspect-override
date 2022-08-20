<?php

use AspectOverride\Lexer\Sequence;
use AspectOverride\Lexer\Token\Token as T;
use AspectOverride\Processors\ClassMethodProcessor;

it('can match AnyOf tokens properly', function() {
    $anyOf = T::anyOf(T::OPENING_BRACKET(), T::CLOSING_BRACKET());
    expect($anyOf->matches(0, '}', '}'))->toBe(Sequence::NEXT())
        ->and($anyOf->matches(0, 'asdf', 'asdf'))->toBe(Sequence::FAIL());
});

it('can match AnyUntil tokens properly', function() {
    $anyOf = T::anyUntil(T::CLOSING_BRACKET());
    expect($anyOf->matches(0, 'asdf', 'asdf'))->toBe(Sequence::REUSE())
        ->and($anyOf->matches(0, '}', '}'))->toBe(Sequence::NEXT());
});

it('can match AnyUntilEmptyStack tokens properly', function() {
    $anyOf = T::anyUntilEmptyStack(T::OPENING_BRACKET(), T::CLOSING_BRACKET());

    expect($anyOf->matches(0, '{', '{'))->toBe(Sequence::REUSE())
        ->and($anyOf->matches(0, '{', '{'))->toBe(Sequence::REUSE())
        ->and($anyOf->matches(0, '{', '{'))->toBe(Sequence::REUSE())
        ->and($anyOf->matches(0, '}', '}'))->toBe(Sequence::REUSE())
        ->and($anyOf->matches(0, '}', '}'))->toBe(Sequence::REUSE())
        ->and($anyOf->matches(0, '}', '}'))->toBe(Sequence::NEXT())

        ->and($anyOf->matches(0, '{', '{'))->toBe(Sequence::REUSE())
        ->and($anyOf->matches(0, '}', '}'))->toBe(Sequence::NEXT())

        ->and($anyOf->matches(0, '{', '{'))->toBe(Sequence::REUSE())
        ->and($anyOf->matches(0, '{', '{'))->toBe(Sequence::REUSE())
        ->and($anyOf->matches(0, '}', '}'))->toBe(Sequence::REUSE())
        ->and($anyOf->matches(0, '{', '{'))->toBe(Sequence::REUSE())
        ->and($anyOf->matches(0, '}', '}'))->toBe(Sequence::REUSE())
        ->and($anyOf->matches(0, '}', '}'))->toBe(Sequence::NEXT());

});

it("can transform code", function() {
    $processor = new ClassMethodProcessor();
    $result = $processor->transform(/** @lang PHP */ "
    class Test {
        public function empty() {
        }
        public function test(\$a, string \$b, &\$c) { return 2; }
    }
    ");
    expect($result)->toBe(/** @lang PHP */ "
    class Test {
        public function empty() {return \AspectOverride\Facades\Instance::wrapAround(__CLASS__, __FUNCTION__, func_get_args(), function(){
        });}
        public function test(\$a, string \$b, &\$c) {return \AspectOverride\Facades\Instance::wrapAround(__CLASS__, __FUNCTION__, func_get_args(), function(\$a, string \$b, &\$c){ return 2; });}
    }
    ");
});
