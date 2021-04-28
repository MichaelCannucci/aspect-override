# Aspect Override

Override methods (Through an aspect oriented approach) and global functions within your PHP tests

## Getting Started

### Installing

```
composer require --dev mcannucci/aspect-override
```

### Bootstraping
Include this within your test runner's bootstrap file
```php
<?php

require __DIR__ . '/../vendor/autoload.php';

// options are:
// setAllowedDirectories => The directory in which php classes are elligble for monkey patching/interception
AspectOverride\Builder::create()
    ->setAllowedDirectories([
        __DIR__ . '/../tests/Util/Fixtures'
    ])
    ->load();
```

### Usage
```php
use AspectOverride\Override;

// for any instances of 'MyClass', return true for the method 'myMethod'
Override::method(MyClass::class|'\App\MyClass', 'myMethod', function(){
  return true;
});
// for any invocation of the global method 'time' return 10
Override::function('time', function(){
  return 10;
})
```

## Versioning

[SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags). 

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgements

Functionality Heavily inspired by [AspectMock](https://github.com/Codeception/AspectMock)
Heavily Inspired by [php-vcr](https://github.com/php-vcr/php-vcr) for monkey-patching through PHP streams
