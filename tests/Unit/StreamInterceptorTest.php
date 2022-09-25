<?php

use AspectOverride\Core\Configuration;
use AspectOverride\Core\Instance;

it("matches file paths properly", function ($path){
    $instance = new Instance(
        Configuration::create()->setDirectories(["/test"])
    );
    expect($instance->shouldProcess($path))->toBeTrue();
})->with([
    "/test/test_php_file.php",
    "/test/../upper_level/test.php"
]);

it("does not match invalid file paths", function($path) {
    $instance = new Instance(
        Configuration::create()->setDirectories(["/test"])
    );
    expect($instance->shouldProcess($path))->toBeTrue();
})->with([
   "/another_folder",
    "/start_with_another_path/but_has/test"
]);