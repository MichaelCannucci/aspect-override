<?php

use AspectOverride\Override;

beforeEach(function() {
    Override::reset();
});

it('can overwrite function return', function () {
    Override::after("TestOverwriteFunctionReturn", "returnTwo", function ($a) {
        return 3;
    });
    evaluate(
        static function () {
            class TestOverwriteFunctionReturn {
                public function returnTwo() {
                    return 2;
                }
            }
            return function() { return (new TestOverwriteFunctionReturn())->returnTwo(); };
        }
    )->toBe(3);
});

it('can overwrite final function return', function () {
    Override::after("TestFinalFunctionReturn", "returnTwo", function ($a) {
        return 3;
    });
    evaluate(
        static function () {
            class TestFinalFunctionReturn {
                final public function returnTwo() {
                    return 2;
                }
            }
            return function() { return (new TestFinalFunctionReturn())->returnTwo(); };
        }
    )->toBe(3);
});

it('can mutate function return', function () {
    Override::after("TestMutableObject", "getMutatableObject", function (MutableObject $obj) {
        $obj->a = 3;
        return $obj;
    });
    evaluate(
        static function () {
            class MutableObject {
                public $a = 1;
            }
            class TestMutableObject {
                public $a;
                public function __construct() {
                    $this->a = new MutableObject();
                }
                public function getMutatableObject() {
                    return $this->a;
                }
            }
            return function() { return (new TestMutableObject())->getMutatableObject()->a; };
        }
    )->toBe(3);
});

it('can return a different anonymous function', function () {
    Override::after("TestDifferentAnonymousFunction", "returnFunction", function (callable $a) {
        return function () use ($a) {
            return $a() + 1;
        };
    });
    evaluate(
        static function () {
            class TestDifferentAnonymousFunction {
                public function returnFunction() {
                    return function () {
                        return 2;
                    };
                }
            }
            return function() { return (new TestDifferentAnonymousFunction())->returnFunction()(); };
        }
    )->toBe(3);
});
