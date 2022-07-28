<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

use Tests\Support\SandboxHelper;

function sandbox(Closure $setup, Closure $injected) {
    $injected = "<?php" . PHP_EOL . SandboxHelper::getCode($injected, true);
    $setup = SandboxHelper::getCode($setup, false);
    $path = SandboxHelper::storeCodeInTemp($injected);
    $directory = dirname($path);
    $cwd = getcwd();
    $runner = /** @lang InjectablePHP */ "
        chdir('$cwd');
        require __DIR__ . '/vendor/autoload.php';
        
        AspectOverride\Facades\Instance::initialize(
        AspectOverride\Core\Configuration::create()
            ->setDirectories([ '$directory' ])
        );
       
        $setup
                    
        require '$path';
    ";
    $command = implode(' ', [PHP_BINARY, "-r", "\"$runner\""]);
    return expect(shell_exec($command));
}
