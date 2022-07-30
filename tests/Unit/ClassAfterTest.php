<?php

use AspectOverride\Override;

it('can overwrite function return', function() {
    sandbox(
        static function() {
            Override::afterMethod("Test", "returnTwo", function($a) {
                return 3;
            });
        },
        static function() {
            class Test {
                public function returnTwo() {
                    return 2;
                }
            }
            echo (new Test)->returnTwo();
        }
    )->toBe(3);
});

it('can mutate function return', function() {
    sandbox(
        static function() {
            Override::afterMethod("Test", "getMutatableObject", function($obj) {
                $obj->a = 3;
                return $obj;
            });
        },
        static function() {
            class MutableObject {
                public $a = 1;
            }
            class Test {
                public $a;
                public function __construct() {
                    $this->a = new MutableObject();
                }
                public function getMutatableObject() {
                    return $this->a;
                }
            }
            echo (new Test)->getMutatableObject()->a;
        }
    )->toBe(3);
});
