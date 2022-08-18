<?php

use AspectOverride\Lexer\Sequence;
use AspectOverride\Lexer\Token\Token as T;

it('AnyOf matches tokens properly', function() {
    $anyOf = T::anyOf(T::OPENING_BRACKET(), T::CLOSING_BRACKET());
    expect($anyOf->matches(0, ')'))->toBe(Sequence::NEXT());
    expect($anyOf->matches(0, 'asdf'))->toBe(Sequence::FAIL());
});

it('AnyUntil matches tokens properly', function() {
    $anyOf = T::anyUntil(T::CLOSING_BRACKET());
    expect($anyOf->matches(0, 'asdf'))->toBe(Sequence::REUSE());
    expect($anyOf->matches(0, '}'))->toBe(Sequence::NEXT());
});

it('AnyUntilEmptyStack matches tokens properly', function() {
    $anyOf = T::anyUntilEmptyStack(T::OPENING_BRACKET(), T::CLOSING_BRACKET());

    expect($anyOf->matches(0, '{'))->toBe(Sequence::REUSE());
    expect($anyOf->matches(0, '{'))->toBe(Sequence::REUSE());
    expect($anyOf->matches(0, '{'))->toBe(Sequence::REUSE());
    expect($anyOf->matches(0, '}'))->toBe(Sequence::REUSE());
    expect($anyOf->matches(0, '}'))->toBe(Sequence::REUSE());
    expect($anyOf->matches(0, '}'))->toBe(Sequence::NEXT());

    expect($anyOf->matches(0, '{'))->toBe(Sequence::REUSE());
    expect($anyOf->matches(0, '}'))->toBe(Sequence::NEXT());

    expect($anyOf->matches(0, '{'))->toBe(Sequence::REUSE());
    expect($anyOf->matches(0, '{'))->toBe(Sequence::REUSE());
    expect($anyOf->matches(0, '}'))->toBe(Sequence::REUSE());
    expect($anyOf->matches(0, '{'))->toBe(Sequence::REUSE());
    expect($anyOf->matches(0, '}'))->toBe(Sequence::REUSE());
    expect($anyOf->matches(0, '}'))->toBe(Sequence::NEXT());
});
