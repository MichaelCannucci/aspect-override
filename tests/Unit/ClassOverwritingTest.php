<?php

namespace Tests\Integration;

use AspectOverride\Override;

it('can overwrite public functions', function () {
    Override::method("TestPublicFunctions", "returnTwo", function () {
        return 3;
    });
    sandbox(
        static function () {
            class TestPublicFunctions {
                public function returnTwo(): int {
                    return 2;
                }
            }
            echo (new TestPublicFunctions())->returnTwo();
        }
    )->toBe(3);
});

it('can overwrite private functions', function () {
    Override::method("TestPrivateFunctions", "returnTwo", function () {
        return 3;
    });
    sandbox(
        static function () {
            class TestPrivateFunctions {
                private function returnTwo(): int {
                    return 2;
                }
                public function callReturnTwo(): int {
                    return $this->returnTwo();
                }
            }
            echo (new TestPrivateFunctions())->callReturnTwo();
        }
    )->toBe(3);
});

it('can overwrite void return functions', function () {
    $called = false;
    Override::method("Test", 'voidReturn', function (&$called) {
        //Note: If the injection points try to return anything this will fail
        $called = true;
    });
    sandbox(
        static function () {
            class TestVoidFunctionReturn {
                public function voidReturn(): void {
                }
            }
            (new TestVoidFunctionReturn())->voidReturn();
        }
    );
    /** @noinspection PhpConditionAlreadyCheckedInspection Override method is called */
    expect($called)->toBeTrue();
});

it('can overwrite protected function', function () {
    Override::method("Test", "returnTwo", function () {
        return 3;
    });
    sandbox(
        static function () {
            class Test {
                protected function returnTwo(): int {
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

it('can overwrite static function', function () {
    Override::method("Test", "returnTwo", function () {
        return 3;
    });
    sandbox(
        static function () {
            class Test {
                public static function returnTwo(): int {
                    return 2;
                }
            }
            echo Test::returnTwo();
        }
    )->toBe(3);
});

it('can overwrite function with no whitespace in body', function () {
    Override::method("Test", "noWhitespace", function () {
        return 3;
    });
    sandbox(
        static function () {
            class Test {
                public static function noWhitespace() {
                }
            }
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            echo Test::noWhitespace();
        }
    )->toBe(3);
});

it('can overwrite empty function', function () {
    Override::method("Test", "empty", function () {
        return 3;
    });
    sandbox(
        static function () {
            class Test {
                public static function empty() {
                }
            }
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            echo Test::empty();
        }
    )->toBe(3);
});

it('can overwrite abstract function', function () {
    Override::method("AbstractClassImplementation", "returnTwo", function () {
        return 3;
    });
    sandbox(
        static function () {
            abstract class AbstractClass {
                abstract public function returnTwo(): int;
            }

            class AbstractClassImplementation extends AbstractClass {
                public function returnTwo(): int {
                    return 2;
                }
            }
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            echo (new AbstractClassImplementation())->returnTwo();
        }
    )->toBe(3);
});

it('can overwrite final function', function () {
    Override::method("AbstractClassImplementation", "returnTwo", function () {
        return 3;
    });
    sandbox(
        static function () {
            class AbstractClassImplementation {
                final public function returnTwo(): int {
                    return 2;
                }
            }
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            echo (new AbstractClassImplementation())->returnTwo();
        }
    )->toBe(3);
});

it('can execute non overwritten functions', function () {
    sandbox(
        static function () {
            class TestClass {
                final public function returnTwo(): int {
                    return 2;
                }
            }
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            echo (new TestClass())->returnTwo();
        }
    )->toBe(2);
});
