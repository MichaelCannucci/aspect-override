<?php

use Tests\Support\TestHelper;

/**
 * @param Closure|string $testCase
 */
function evaluate($testCase) {
    $testCase = is_string($testCase) ? $testCase : TestHelper::getCode($testCase);
    $file = TestHelper::storeCode(__DIR__ . '/../tmp/code', $testCase);

    $result = (require $file)();
    return expect($result);
}
