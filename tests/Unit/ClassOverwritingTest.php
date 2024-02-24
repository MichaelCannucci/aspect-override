<?php

namespace Tests\Integration;

use AspectOverride\Override;

it('can overwrite public functions', function () {
    Override::method("TestPublicFunctions", "returnTwo", function () {
        return 3;
    });
    evaluate(
        static function () {
            class TestPublicFunctions {
                public function returnTwo(): int {
                    return 2;
                }
            }

            return (new TestPublicFunctions())->returnTwo();
        }
    )->toBe(3);
});

it('can overwrite private functions', function () {
    Override::method("TestPrivateFunctions", "returnTwo", function () {
        return 3;
    });
    evaluate(
        static function () {
            class TestPrivateFunctions {
                private function returnTwo(): int {
                    return 2;
                }
                public function callReturnTwo(): int {
                    return $this->returnTwo();
                }
            }

            return (new TestPrivateFunctions())->callReturnTwo();
        }
    )->toBe(3);
});

it('can overwrite void return functions', function () {
    $called = false;
    Override::method("TestVoidFunctionReturn", 'voidReturn', function () use (&$called) {
        //Note: If the injection points try to return anything this will fail
        $called = true;
    });
    evaluate(
        static function () {
            class TestVoidFunctionReturn {
                public function voidReturn(): void {
                }
            }

            (new TestVoidFunctionReturn())->voidReturn();
        }
    );
    expect($called)->toBeTrue();
});

it('can overwrite protected function', function () {
    Override::method("TestOverwriteProtectedFunction", "returnTwo", function () {
        return 3;
    });
    evaluate(
        static function () {
            class TestOverwriteProtectedFunction {
                protected function returnTwo(): int {
                    return 2;
                }
                public function callReturnTwo(): int {
                    return $this->returnTwo();
                }
            }

            return (new TestOverwriteProtectedFunction())->callReturnTwo();
        }
    )->toBe(3);
});

it('can overwrite static function', function () {
    Override::method("TestOverwriteStaticFunction", "returnTwo", function () {
        return 3;
    });
    evaluate(
        static function () {
            class TestOverwriteStaticFunction {
                public static function returnTwo(): int {
                    return 2;
                }
            }

            return TestOverwriteStaticFunction::returnTwo();
        }
    )->toBe(3);
});

it('can overwrite function with no whitespace in body', function () {
    Override::method("TestWhitespaceBody", "noWhitespace", function () {
        return 3;
    });
    evaluate(
        static function () {
            class TestWhitespaceBody {
                public static function noWhitespace(){}
            }

            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return TestWhitespaceBody::noWhitespace();
        }
    )->toBe(3);
});

it('can overwrite empty function', function () {
    Override::method("TestEmptyFunction", "empty", function () {
        return 3;
    });
    evaluate(
        static function () {
            class TestEmptyFunction {
                public static function empty() {
                }
            }

            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return TestEmptyFunction::empty();
        }
    )->toBe(3);
});

it('can overwrite function with reserved keyword as name', function () {
    Override::method("TestReservedKeyword", "isset", function () {
        return 3;
    });
    evaluate(
        static function () {
            class TestReservedKeyword {
                public static function isset() {
                }
            }


            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return TestReservedKeyword::isset();
        }
    )->toBe(3);
});

it('can overwrite abstract function', function () {
    Override::method("AbstractClassImplementation", "returnTwo", function () {
        return 3;
    });
    evaluate(
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
            return (new AbstractClassImplementation())->returnTwo();
        }
    )->toBe(3);
});

it('can overwrite final function', function () {
    Override::method("TestFinalFunction", "returnTwo", function () {
        return 3;
    });
    evaluate(
        static function () {
            class TestFinalFunction {
                final public function returnTwo(): int {
                    return 2;
                }
            }

            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return (new TestFinalFunction())->returnTwo();
        }
    )->toBe(3);
});

it('can execute non overwritten functions', function () {
    evaluate(
        static function () {
            class TestNoOverwrites {
                final public function returnTwo(): int {
                    return 2;
                }
            }

            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return (new TestNoOverwrites())->returnTwo();
        }
    )->toBe(2);
});
