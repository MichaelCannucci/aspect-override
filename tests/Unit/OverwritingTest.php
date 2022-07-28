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

it('can_overwrite_void_return_functions', function() {
    sandbox(
        static function() {
            Override::method("Test", 'voidReturn', function () {
                // If the injection points try to return anything this will fail
            });
        },
        static function() {
            class Test {
                public function voidReturn(): void {

                }
            }
            echo "void!";
            (new Test())->voidReturn();
        }
    )->toBe("void!");
});

it('can_overwrite_protected_function', function() {
    sandbox(
        static function() {
            Override::method("Test", "returnTwo", function() {
                return 3;
            });
        },
        static function() {
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

it('can_overwrite_static_function', function() {
    sandbox(
        static function() {
            Override::method("Test", "returnTwo", function() {
                return 3;
            });
        },
        static function() {
            class Test {
                public static function returnTwo(): int {
                    return 2;
                }
            }
            echo Test::returnTwo();
        }
    )->toBe(3);
});

it('can_overwrite_function_with_no_whitespace_in_body', function() {
    sandbox(
        static function() {
            Override::method("Test", "noWhitespace", function() {
                return 3;
            });
        },
        static function() {
            class Test {
                public static function noWhitespace(){}
            }
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            echo Test::noWhitespace();
        }
    )->toBe(3);
});

it('can_overwrite_empty_function', function() {
    sandbox(
        static function() {
            Override::method("Test", "empty", function() {
                return 3;
            });
        },
        static function() {
            class Test {
                public static function empty(){

                }
            }
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            echo Test::empty();
        }
    )->toBe(3);
});

it('can_overwrite_abstract_function', function() {
    sandbox(
        static function() {
            Override::method("AbstractClassImplementation", "returnTwo", function() {
                return 3;
            });
        },
        static function() {
            abstract class AbstractClass
            {
                abstract function returnTwo(): int;
            }

            class AbstractClassImplementation extends AbstractClass
            {
                function returnTwo(): int {
                    return 2;
                }
            }
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            echo (new AbstractClassImplementation)->returnTwo();
        }
    )->toBe(3);
});