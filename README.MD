# Aspect Override

Override methods (Through an aspect oriented approach) and functions within your PHP tests

**Note**: This library is only intended to be used in your testing environment and has no guarantees of stability in production environments

## About

This library aims to be the 'swiss army knife' of being able to test any PHP project by allowing you to modify the 
execution of the program however you wish.


## Features

- Overwrite any type of class method static or non-static
- Overwrite the functionality of any function whether it's namespaced or global
- Overwrite the arguments of a function before it's called
- Overwrite the return of a function after it's called
- Modify function arguments even if they are passed by reference
- Framework and autoloader agnostic, works with any PHP codebase (ex: Composer, regular require path/to/my/code.php)

## Getting Started

### Installing

```
composer require --dev mcannucci/aspect-override
```

## Bootstrapping
To enable overwriting function and class method, AspectOverride needs to initialize some stream processors
to perform the code transformations that enable the class method and function rewriting
```php
<?php

require __DIR__ . '/../vendor/autoload.php';

AspectOverride\Facades\Instance::initialize(
    AspectOverride\Core\Configuration::create()
        ->setDirectories([
            __DIR__ . '/../app'
        ])
        ->setExcludedDirectories([
            __DIR__ . '/../app/excluded'
        ])
);
```

## Usage
### Overwriting class methods
```php
use AspectOverride\Override;

class MyClass {
   public function myMethod() {
      return false;
   }
   public static function myStaticMethod() {
      return false;
   }
}

// for any instance of 'MyClass', return true for the method 'myMethod' and 'myStaticMethod' instead of false
Override::method(MyClass::class, 'myMethod', function(){
  return true;
});

Override::method(MyClass::class, 'myStaticMethod', function(){
  return true;
});

// Will work if it's a static or instantiated method
MyClass::myStaticMethod() // true;
(new MyClass)->myMethod(); // true;
```
### Overwriting class methods arguments before running
```php
use AspectOverride\Override;

class MyClass {
   public static function echoThis(int $a) {
      echo $a;
   }
}

// Before the function 'echoThis' runs we change $a to be incremented
Override::method(MyClass::class, 'echoThis', function(int $a){
  return [$a + 1]
});

MyClass::echoThis(2) // 3;
```

### Overwriting specific class methods arguments before running
```php
use AspectOverride\Override;

class MyClass {
   public static function echoSecondArg(int $a, int $b) {
      echo $b;
   }
}

// Before the function 'echoSecondArg' runs we only modify the second argument and keep the first one as is
Override::beforeMethod(MyClass::class, 'echoSecondArg', function(int $a, int $b){
  return ['b' => $b + 1]
});

MyClass::echoSecondArg(2,3) // 4;
```

### Overwriting a method's return value
```php
use AspectOverride\Override;

class MyClass {
   public static function echoOne() {
      echo 1;
   }
}

// After the function 'echoOne' runs, we increment the result by one 
Override::afterMethod(MyClass::class, 'echoOne', function($a){
  return $a + 1;
});

MyClass::echoOne() // 2;
```

### Overwriting a function's return value
```php
use AspectOverride\Override;

Override::function('time', function(){
  return 1000;
});

time() // 1000;
```

## Questions you might have
**Q:** How does this work, I thought you can't redefine method and function in PHP?

**A:** When a file is loaded in PHP it passes through PHP's [streams](https://www.php.net/manual/en/intro.stream.php)
and since PHP supports wrappers around streams we can rewrite the code to include the overwrite functionality before PHP executes it 

##
**Q:** This library is really weird and I hate it, why don't you test code normally?

**A:** I wish all the codebases I work on are testable 😈
##
**Q:** Why don't line numbers match or step over requires multiple clicks up when step debugging 

**A:** Since we're rewriting the source code to rewrite methods and functions. statements get inserted 
to return early with another value, replace arguments and or call and return the result of 'rewritten' function.
This seemed to be better than having to step debug overwritten code (If possible)

## Versioning

[SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags). 

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgements

Functionality Heavily inspired by [AspectMock](https://github.com/Codeception/AspectMock) and
Heavily Inspired by [php-vcr](https://github.com/php-vcr/php-vcr) for monkey-patching through PHP streams
