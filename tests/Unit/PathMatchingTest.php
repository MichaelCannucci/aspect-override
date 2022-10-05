<?php

use AspectOverride\Core\Configuration;
use AspectOverride\Core\FileChecker;
use AspectOverride\Core\Instance;

it("matches file paths properly", function ($path){
    $checker = new FileChecker(
        Configuration::create()
            ->setDirectories(["/test"])
            ->setExcludedDirectories(["/excluded"])
    );
    expect($checker->shouldProcess($path))->toBeTrue();
})->with([
    "/test/test_php_file.php",
    "/test/upper_level/../test.php",
    "/test/a/../path.php",
    "/test/has/excluded/later/on.php",
]);

it("does not match invalid file paths or excluded", function($path) {
    $checker = new FileChecker(
        Configuration::create()
            ->setDirectories(["/test"])
            ->setExcludedDirectories(["/excluded"])
    );
    expect($checker->shouldProcess($path))->toBeFalse();
})->with([
    "/another_folder/file.php",
    "/another_folder/with/a/file.php",
    "/start_with_another_path/but_has/test.php",
    "/excluded/no match.php",
    "/random/but/../fails/because/not/starting/with/test.php"
]);