<?php

use AspectOverride\Override;

it('can overwrite function arguments', function () {
    sandbox(
        static function () {
            Override::beforeMethod("Test", "echoArgs", function ($a) {
                return [3];
            });
        },
        static function () {
            class Test {
                public function echoArgs($a) {
                    echo $a;
                }
            }
            (new Test())->echoArgs(2);
        }
    )->toBe(3);
});

it('can overwrite final function arguments', function () {
    sandbox(
        static function () {
            Override::beforeMethod("Test", "echoArgs", function ($a) {
                return [3];
            });
        },
        static function () {
            class Test {
                final public function echoArgs($a) {
                    echo $a;
                }
            }
            (new Test())->echoArgs(2);
        }
    )->toBe(3);
});

it('can overwrite multiple function arguments', function () {
    sandbox(
        static function () {
            Override::beforeMethod("Test", "echoSecondArg", function ($a, $b, $c) {
                return [$a, 3, $c];
            });
        },
        static function () {
            class Test {
                public function echoSecondArg($a, $b, $c) {
                    echo $b;
                }
            }
            (new Test())->echoSecondArg(2, 2, 2);
        }
    )->toBe(3);
});

it('respects pass by ref', function () {
    sandbox(
        static function () {
            Override::beforeMethod("Test", 'doThingToRef', function ($a) {
                // Since 'extract' is what sets the variables, we don't have to modify the reference ourselves
                return [3];
            });
        },
        static function () {
            class Test {
                public function doThingToRef(&$a) {
                    $a = 2;
                }
            }
            $a = 1;
            (new Test())->doThingToRef($a);
            echo $a;
        }
    )->toBe(2);
});
