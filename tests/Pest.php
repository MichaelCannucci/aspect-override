<?php

use Tests\Support\SandboxHelper;

/**
 * @param Closure|string $testCase
 */
function sandbox($testCase) {
    $testCase = is_string($testCase) ? $testCase : SandboxHelper::getCode($testCase);
    $file = SandboxHelper::storeCode(__DIR__ . '/../tmp/code', $testCase);

    $result = require $file;
    return expect($result);
}
