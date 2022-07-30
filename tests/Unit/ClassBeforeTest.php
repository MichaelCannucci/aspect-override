<?php

use AspectOverride\Override;

it('can overwrite function arguments', function() {
    sandbox(
        static function() {
            Override::beforeMethod("Test", "echoArgs", function($a) {
                return [3];
            });
        },
        static function() {
            class Test {
                public function echoArgs($a) {
                    echo $a;
                }
            }
            (new Test)->echoArgs(2);
        }
    )->toBe(3);
});

it('can overwrite specific function arguments', function() {
    sandbox(
        static function() {
            Override::beforeMethod("Test", "echoSecondArg", function($a, $b, $c) {
                return ['b' => 3];
            });
        },
        static function() {
            class Test {
                public function echoSecondArg($a, $b, $c) {
                    echo $b;
                }
            }
            (new Test)->echoSecondArg(2, 2, 2);
        }
    )->toBe(3);
});
