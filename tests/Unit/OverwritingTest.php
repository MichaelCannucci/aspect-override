<?php

namespace Tests\Integration;

use AspectOverride\Override;


it('can_overwrite_public_functions', function() {
    sandbox(
        static function() {
            Override::method("Test", "returnTwo", function() {
                return 3;
            });
        },
        static function() {
            class Test {
                public function returnTwo(): int {
                    return 2;
                }
            }
            echo (new Test())->returnTwo();
        }
    )->toBe(3);
});

it('can_overwrite_private_functions', function() {
    sandbox(
        static function() {
            Override::method("Test", "returnTwo", function() {
                return 3;
            });
        },
        static function() {
            class Test {
                private function returnTwo(): int {
                    return 2;
                }
                public function callReturnTwo(): int {
                    return $this->returnTwo();
                }
            }
            echo (new Test())->callReturnTwo();
        }
    )->toBe(3);
});