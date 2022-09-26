<?php

use AspectOverride\Override;

it('can overwrite function arguments', function () {
    Override::before("TestFunctionArgs", "returnArg", function (&$a) {
        $a = 3;
    });
    evaluate(
        static function () {
            class TestFunctionArgs {
                public function returnArg($a) {
                    return $a;
                }
            }
            return function() { return (new TestFunctionArgs())->returnArg(2); };
        }
    )->toBe(3);
});

it('can overwrite final function arguments', function () {
    Override::before("TestFinalFunctionBefore", "returnArg", function (&$a) {
        $a = 3;
    });
    evaluate(
        static function () {
            class TestFinalFunctionBefore {
                final public function returnArg($a) {
                    return $a;
                }
            }
            return function() { return (new TestFinalFunctionBefore())->returnArg(2); };
        }
    )->toBe(3);
});

it('can overwrite multiple function arguments', function () {
    Override::before("TestOverwriteMultipleArgs", "returnSecondArg", function ($a, &$b, $c) {
        $b = 3;
    });
    evaluate(
        static function () {
            class TestOverwriteMultipleArgs {
                public function returnSecondArg($a, $b, $c) {
                    return $b;
                }
            }
            return function() { return (new TestOverwriteMultipleArgs())->returnSecondArg(2, 2, 2); };
        }
    )->toBe(3);
});

it('respects pass by ref', function () {
    Override::before("Test", 'doThingToRef', function (&$a) {
        $a = 3; // Should be overwritten when the actual call happens
    });
    evaluate(
        static function () {
            class TestRespectPassByRef {
                public function doThingToRef(&$a) {
                    $a = 2;
                }
            }
            return function() {
                $a = 1;
                (new TestRespectPassByRef())->doThingToRef($a);
                return $a;
            };
        }
    )->toBe(2);
});
