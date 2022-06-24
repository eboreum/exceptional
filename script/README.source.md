Eboreum/Exceptional: Create and format PHP exceptions easily
===============================

![license](https://img.shields.io/github/license/eboreum/exceptional?label=license)
![build](https://github.com/eboreum/exceptional/workflows/build/badge.svg?branch=main)
[![Code Coverage](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/kafoso/a6e4ffd4089e5e6a13e307c707849eb7/raw/test-coverage__main.json)](https://github.com/eboreum/exceptional/actions)
[![PHPStan Level](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/kafoso/a6e4ffd4089e5e6a13e307c707849eb7/raw/phpstan-level__main.json)](https://github.com/eboreum/exceptional/actions)

%composer.json.description%

When a method is called, and somehow that leads to an exception/throwable being raised, wouldn't it be nice knowing all arguments a method was called with? Exceptional can unravel that for you and present these arguments with their respective names in a concise and meaningful way. Additionally, the integration with Eboreum/Caster (https://packagist.org/packages/eboreum/caster) allows revealing of information about the object within which the exception/error occured. This is sometimes valuable and crucial information, and it is superb for debugging.

<a name="requirements"></a>
# Requirements

%composer.json.require%

For more information, see the [`composer.json`](composer.json) file.

# Installation

Via [Composer](https://getcomposer.org/) (https://packagist.org/packages/eboreum/exceptional):

    composer install eboreum/exceptional

Via GitHub:

    git clone git@github.com:eboreum/exceptional.git

# Fundamentals

## Exception message generation

### Example 1: The basics

**Example:**

```php
%include "script/misc/readme/exception-message-generation/example-1-the-basics.php"%
```

**Output:**

```
%run "script/misc/readme/exception-message-generation/example-1-the-basics.php"%
```

Notice how each argument is paired with its respective values from the `func_get_args()` function. The argument `$c` has even received its default value, which `func_get_args()` will **not** return.

### Example 2: Providing more arguments than there are named arguments

**Example:**

```php
%include "script/misc/readme/exception-message-generation/example-2-providing-more-arguments-than-there-are-named-arguments.php"%
```

**Output:**

```
%run "script/misc/readme/exception-message-generation/example-2-providing-more-arguments-than-there-are-named-arguments.php"%
```

Notice how `$a` and `$b` are named, but the unnamed arguments have received their respective indexes, `{2}` and `{3}`.

### Example 3: A constant as default value

**Example:**

```php
%include "script/misc/readme/exception-message-generation/example-3-a-constant-as-default-value.php"%
```

**Output:**

```
%run "script/misc/readme/exception-message-generation/example-3-a-constant-as-default-value.php"%
```

Argument `$a` has received its default value from the class constant `Fooc261bae9da674d679de77a943ae57779::SOME_CONSTANT`, `$b` has received its default value from the class constant `Fooaea91664ed3d4467aeb2dfabb2623b53::SOME_PARENT_CONSTANT`, and `$c` has received its default value from the global constant `GLOBAL_CONSTANT_25b105757d32443188cca9c7646ccfe6`.

### Example 4: Static method call

**Example:**

```php
%include "script/misc/readme/exception-message-generation/example-4-static-method-call.php"%
```

**Output:**

```
%run "script/misc/readme/exception-message-generation/example-4-static-method-call.php"%
```

Notice how instead of `$this`, `static::class` is used.

### Example 5: Making object descriptions verbose using caster

Wouldn't it be nice if we, in addition to the method argument snitching, could get additional information about the object within which the method failed? We can do just that using the `Eboreum\Caster\Caster` integration.

**Example:**

```php
%include "script/misc/readme/exception-message-generation/example-5-making-object-descriptions-verbose-using-caster.php"%
```

**Output:**

```
%run "script/misc/readme/exception-message-generation/example-5-making-object-descriptions-verbose-using-caster.php"%
```

Notice how we now get useful information from the above object, its ID being 42 (and argument `$a` is 7).

You must use `$this` as the argument in the `makeFailureInMethodMessage` call (and **not** `static::class`) for the above to work.

## Exception formatters

### Example 1: Default formatter

Class: `Eboreum\Exceptional\Formatting\DefaultFormatter`

A plain text formatter. Contains line breaks and indentation.

```php
%include "script/misc/readme/formatter/example-1-defaultformatter.php"%
```

**Output:**

```
%run "script/misc/readme/formatter/example-1-defaultformatter.php"%
```

### Example 2: HTML5 `<table>` formatter

Class: `Eboreum\Exceptional\Formatting\HTML5TableFormatter`

Formats the throwable as HTML5 `<table>`.

```php
%include "script/misc/readme/formatter/example-2-html5tableformatter.php"%
```

**Output:**

```
%run "script/misc/readme/formatter/example-2-html5tableformatter.php"%
```

### Example 3: JSON formatter

Class: `Eboreum\Exceptional\Formatting\JSONFormatter`

Formats the throwable as JSON.

```php
%include "script/misc/readme/formatter/example-3-jsonformatter.php"%
```

**Output:**

```
%run "script/misc/readme/formatter/example-3-jsonformatter.php"%
```

### Example 4: Oneline formatter

Class: `Eboreum\Exceptional\Formatting\OnelineFormatter`

Formats the throwable as string with all its contents on a single line. Great for (improved) output in error logs, which do not allow line breaks.

```php
%include "script/misc/readme/formatter/example-4-onelineformatter.php"%
```

**Output:**

```
%run "script/misc/readme/formatter/example-4-onelineformatter.php"%
```

### Example 5: XML formatter

Class: `Eboreum\Exceptional\Formatting\XMLFormatter`

Formats the throwable as XML.

```php
%include "script/misc/readme/formatter/example-5-xmlformatter.php"%
```

**Output:**

```
%run "script/misc/readme/formatter/example-5-xmlformatter.php"%
```

## Test/development requirements

%composer.json.require-dev%

## Running tests

For all unit tests, first follow these steps:

```
cd tests
php ../vendor/bin/phpunit
```

# License & Disclaimer

See [`LICENSE`](LICENSE) file. Basically: Use this library at your own risk.

# Contributing

We prefer that you create a ticket and or a pull request at https://github.com/eboreum/exceptional, and have a discussion about a feature or bug here.

# Credits

## Authors

%composer.json.authors%
