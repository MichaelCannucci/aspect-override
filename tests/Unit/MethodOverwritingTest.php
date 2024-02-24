<?php

use AspectOverride\Override;

it("can overwrite method in declared namespace", function () {
    Override::function('time', function () {
        return 3;
    });
    evaluate(
        /** @lang PHP */
        "<?php
        namespace test;
        
        return time();
        "
    )->toBe(3);
});

it("can overwrite method in scoped namespace", function () {
    Override::function('time', function () {
        return 3;
    });
    evaluate(
        /** @lang PHP */
        "<?php
        namespace test {
            return time();
        }
        "
    )->toBe(3);
});

it("can overwrite method in multiple declared namespaces", function () {
    Override::function('time', function () {
        return 3;
    });
    evaluate(
        /** @lang PHP */
        '<?php
        namespace test;
        $a = time();
        namespace testing;
        $b = time();

        return $a + $b;
        '
    )->toBe(6);
});

it("can overwrite method in multiple scoped namespaces", function () {
    Override::function('time', function () {
        return 3;
    });
    evaluate(
        /** @lang PHP */
        '<?php
        namespace test {
            $a = time();
        }
        namespace testing {
            $b = time();
        }
        namespace {
            return $a + $b;   
        }
        '
    )->toBe(6);
});

it("can overwrite method in nested namespace", function () {
    Override::function('time', function () {
        return 3;
    });
    evaluate(
        /** @lang PHP */
        "<?php
        namespace test\in\a\path;
        return time();
        "
    )->toBe(3);
});

it("can overwrite reference variables of function", function () {
    Override::function('array_shift', function (array &$array) {
        $array = [1,2,3];
        return 3;
    });
    evaluate(
        static function() {
            $array = [3,4,5];
            $buffer = "";
            $buffer .= array_shift($array);
            foreach ($array as $item) {
                $buffer .= $item;
            }

            return $buffer;
        }
    )->toBe("3123");
});

it("can overwrite method in chained calls", function() {
    function test_function($a) { return $a + 1; }

    Override::function('test_function', function ($a) {
        return $a + 3;
    });
    evaluate(
        static function() {
            return test_function(test_function(test_function(1)));
        }
    )->toBe(10);
});
