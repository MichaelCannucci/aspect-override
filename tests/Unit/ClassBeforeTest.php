<?php

use AspectOverride\Override;

it('can overwrite function arguments', function () {
    Override::before("TestFunctionArgs", "echoArgs", function (&$a) {
        $a = 3;
    });
    sandbox(
        static function () {
            class TestFunctionArgs {
                public function echoArgs($a) {
                    echo $a;
                }
            }
            (new TestFunctionArgs())->echoArgs(2);
        }
    )->toBe(3);
});

it('can overwrite final function arguments', function () {
    Override::before("TestFinalFunctionBefore", "echoArgs", function (&$a) {
        $a = 3;
    });
    sandbox(
        static function () {
            class TestFinalFunctionBefore {
                final public function echoArgs($a) {
                    echo $a;
                }
            }
            (new TestFinalFunctionBefore())->echoArgs(2);
        }
    )->toBe(3);
});

it('can overwrite multiple function arguments', function () {
    Override::before("TestOverwriteMultipleArgs", "echoSecondArg", function ($a, &$b, $c) {
        $b = 3;
    });
    sandbox(
        static function () {
            class TestOverwriteMultipleArgs {
                public function echoSecondArg($a, $b, $c) {
                    echo $b;
                }
            }
            (new TestOverwriteMultipleArgs())->echoSecondArg(2, 2, 2);
        }
    )->toBe(3);
});

it('respects pass by ref', function () {
    Override::before("Test", 'doThingToRef', function (&$a) {
        $a = 3; // Should be overwritten when the actual call happens
    });
    sandbox(
        static function () {
            class TestRespectPassByRef {
                public function doThingToRef(&$a) {
                    $a = 2;
                }
            }
            $a = 1;
            (new TestRespectPassByRef())->doThingToRef($a);
            echo $a;
        }
    )->toBe(2);
});
