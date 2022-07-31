<?php

namespace Tests\Integration;

use AspectOverride\Override;

it('can overwrite public functions', function() {
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

it('can overwrite private functions', function() {
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

it('can overwrite void return functions', function() {
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

it('can overwrite protected function', function() {
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

it('can overwrite static function', function() {
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

it('can overwrite function with no whitespace in body', function() {
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

it('can overwrite empty function', function() {
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

it('can overwrite abstract function', function() {
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

it('can overwrite final function', function() {
    sandbox(
        static function() {
            Override::method("AbstractClassImplementation", "returnTwo", function() {
                return 3;
            });
        },
        static function() {
            class AbstractClassImplementation
            {
                final function returnTwo(): int {
                    return 2;
                }
            }
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            echo (new AbstractClassImplementation)->returnTwo();
        }
    )->toBe(3);
});