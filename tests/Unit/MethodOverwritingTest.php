<?php

use AspectOverride\Override;

it("can overwrite method in declared namespace", function() {
    sandbox(
        static function() {
            Override::function('time', function() {
                return 3;
            });
        },
        /** @lang PHP */ "<?php
        namespace test;
        
        echo time();
        "
    )->toBe(3);
});

it("can overwrite method in scoped namespace", function() {
    sandbox(
        static function() {
            Override::function('time', function() {
                return 3;
            });
        },
        /** @lang PHP */ "<?php
        namespace test {
            echo time();
        }
        "
    )->toBe(3);
});

it("can overwrite method in multiple declared namespaces", function() {
    sandbox(
        static function() {
            Override::function('time', function() {
                return 3;
            });
        },
        /** @lang PHP */ "<?php
        namespace test;
        echo time();
        namespace testing;
        echo time();
        "
    )->toBe(33);
});

it("can overwrite method in multiple scoped namespaces", function() {
    sandbox(
        static function() {
            Override::function('time', function() {
                return 3;
            });
        },
        /** @lang PHP */ "<?php
        namespace test {
            echo time();
        }
        namespace testing {
            echo time();
        }
        "
    )->toBe(33);
});

it("can overwrite method in nested namespace", function() {
    sandbox(
        static function() {
            Override::function('time', function() {
                return 3;
            });
        },
        /** @lang PHP */ "<?php
        namespace test\in\a\path;
        echo time();
        "
    )->toBe(3);
});

it("can overwrite reference variables of function", function() {
    sandbox(
        static function() {
            Override::function('array_shift', function(array &$array) {
                $array = [1,2,3];
                return 3;
            });
        },
        /** @lang PHP */ "<?php
        namespace test;
        \$array = [3,4,5];
        echo array_shift(\$array);
        foreach (\$array as \$item) {
            echo \$item;
        }
        "
    )->toBe(3123);
});