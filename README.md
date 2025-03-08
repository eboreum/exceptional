Eboreum/Exceptional: Create and format PHP exceptions easily
===============================

![license](https://img.shields.io/github/license/eboreum/exceptional?label=license)
![build](https://github.com/eboreum/exceptional/workflows/build/badge.svg?branch=main)
[![Code Coverage](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/kafoso/a6e4ffd4089e5e6a13e307c707849eb7/raw/test-coverage__main.json)](https://github.com/eboreum/exceptional/actions)
[![PHPStan Level](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/kafoso/a6e4ffd4089e5e6a13e307c707849eb7/raw/phpstan-level__main.json)](https://github.com/eboreum/exceptional/actions)

Create and format PHP exceptions easily. Automatically unravel method arguments. Ensure that sensitive strings like passwords, tokens, PHPSESSID, etc. are being masked and thus will instead appear as e.g. "******" in the resulting text.

[comment]: # (The README.md is generated using `script/generate-readme.php`)


When a method is called, and somehow that leads to an exception/throwable being raised, wouldn't it be nice knowing all arguments a method was called with? Exceptional can unravel that for you and present these arguments with their respective names in a concise and meaningful way. Additionally, the integration with Eboreum/Caster (https://packagist.org/packages/eboreum/caster) allows revealing of information about the object within which the exception/error occured. This is sometimes valuable and crucial information, and it is superb for debugging.

<a name="requirements"></a>
# Requirements

```json
"php": "^8.3",
"eboreum/caster": "^2.1"
```

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
<?php

use Eboreum\Exceptional\ExceptionMessageGenerator;

class Foo377464ece90d4b918254101d596d90a8
{
    /**
     * @throws RuntimeException
     */
    public function bar(int $a, bool $b, ?string $c = null): string
    {
        throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
            $this,
            new ReflectionMethod(self::class, __FUNCTION__),
            func_get_args(),
        ));
    }
}

$foo = new Foo377464ece90d4b918254101d596d90a8();

try {
    $foo->bar(42, true);
} catch (RuntimeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

```

**Output:**

```
Failure in \Foo377464ece90d4b918254101d596d90a8->bar($a = (int) 42, $b = (bool) true, $c = (null) null) inside (object) \Foo377464ece90d4b918254101d596d90a8

```

Notice how each argument is paired with its respective values from the `func_get_args()` function. The argument `$c` has even received its default value, which `func_get_args()` will **not** return.

### Example 2: Providing more arguments than there are named arguments

**Example:**

```php
<?php

use Eboreum\Exceptional\ExceptionMessageGenerator;

class Foo1ff07b0e563e4efbb5a5280f7fe412d8
{
    /**
     * @throws RuntimeException
     */
    public function bar(int $a, bool $b): string
    {
        throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
            $this,
            new ReflectionMethod(self::class, __FUNCTION__),
            func_get_args(),
        ));
    }
}

$foo = new Foo1ff07b0e563e4efbb5a5280f7fe412d8();

try {
    $foo->bar(42, true, null, 'hello');
} catch (RuntimeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

```

**Output:**

```
Failure in \Foo1ff07b0e563e4efbb5a5280f7fe412d8->bar($a = (int) 42, $b = (bool) true, {2} = (null) null, {3} = (string(5)) "hello") inside (object) \Foo1ff07b0e563e4efbb5a5280f7fe412d8

```

Notice how `$a` and `$b` are named, but the unnamed arguments have received their respective indexes, `{2}` and `{3}`.

### Example 3: A constant as default value

**Example:**

```php
<?php

use Eboreum\Exceptional\ExceptionMessageGenerator;

class Fooaea91664ed3d4467aeb2dfabb2623b53
{
    public const SOME_PARENT_CONSTANT = 42;
}

class Fooc261bae9da674d679de77a943ae57779 extends Fooaea91664ed3d4467aeb2dfabb2623b53
{
    public const SOME_CONSTANT = 3.14;

    /**
     * @throws RuntimeException
     */
    public function bar(
        float $a = self::SOME_CONSTANT,
        int $b = self::SOME_PARENT_CONSTANT,
        int $c = PHP_INT_MAX
    ): void {
        throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
            $this,
            new ReflectionMethod(self::class, __FUNCTION__),
            func_get_args(),
        ));
    }
}

$foo = new Fooc261bae9da674d679de77a943ae57779();

try {
    $foo->bar();
} catch (RuntimeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

```

**Output:**

```
Failure in \Fooc261bae9da674d679de77a943ae57779->bar($a = (float) 3.14, $b = (int) 42, $c = (int) 9223372036854775807) inside (object) \Fooc261bae9da674d679de77a943ae57779

```

Argument `$a` has received its default value from the class constant `Fooc261bae9da674d679de77a943ae57779::SOME_CONSTANT`, `$b` has received its default value from the class constant `Fooaea91664ed3d4467aeb2dfabb2623b53::SOME_PARENT_CONSTANT`, and `$c` has received its default value from the global constant `GLOBAL_CONSTANT_25b105757d32443188cca9c7646ccfe6`.

### Example 4: Static method call

**Example:**

```php
<?php

use Eboreum\Exceptional\ExceptionMessageGenerator;

class Foo1a7c13d6ce9f4646a120041e36717d5a
{
    /**
     * @throws RuntimeException
     */
    public static function bar(int $a): string
    {
        throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
            static::class,
            new ReflectionMethod(self::class, __FUNCTION__),
            func_get_args(),
        ));
    }
}


try {
    Foo1a7c13d6ce9f4646a120041e36717d5a::bar(42);
} catch (RuntimeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

```

**Output:**

```
Failure in \Foo1a7c13d6ce9f4646a120041e36717d5a::bar($a = (int) 42) inside (class) \Foo1a7c13d6ce9f4646a120041e36717d5a

```

Notice how instead of `$this`, `static::class` is used.

### Example 5: Making object descriptions verbose using caster

Wouldn't it be nice if we, in addition to the method argument snitching, could get additional information about the object within which the method failed? We can do just that using the `Eboreum\Caster\Caster` integration.

**Example:**

```php
<?php

use Eboreum\Caster\Attribute\DebugIdentifier;
use Eboreum\Caster\Collection\Formatter\ObjectFormatterCollection;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Caster\Contract\DebugIdentifierAttributeInterface;
use Eboreum\Caster\Contract\Formatter\ObjectFormatterInterface;
use Eboreum\Caster\Contract\TextuallyIdentifiableInterface;
use Eboreum\Caster\Formatter\Object_\DebugIdentifierAttributeInterfaceFormatter;
use Eboreum\Caster\Formatter\Object_\TextuallyIdentifiableInterfaceFormatter;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\ExceptionMessageGenerator;

// Using TextuallyIdentifiableInterface

class Foo1990801ff8324df1b73e323d7fca71a8 implements TextuallyIdentifiableInterface
{
    protected int $id = 42;

    /**
     * @throws RuntimeException
     */
    public function bar(int $a): string
    {
        /** @var array<ObjectFormatterInterface> $formatters */
        $formatters = [new TextuallyIdentifiableInterfaceFormatter()];

        $caster = Caster::getInstance();
        $caster = $caster->withCustomObjectFormatterCollection(new ObjectFormatterCollection($formatters));

        $exceptionMessageGenerator = ExceptionMessageGenerator::getInstance()->withCaster($caster);

        throw new RuntimeException($exceptionMessageGenerator->makeFailureInMethodMessage(
            $this,
            new ReflectionMethod(self::class, __FUNCTION__),
            func_get_args(),
        ));
    }

    public function toTextualIdentifier(CasterInterface $caster): string
    {
        return sprintf(
            'My ID is: %d',
            $this->id,
        );
    }
}


$foo = new Foo1990801ff8324df1b73e323d7fca71a8();

try {
    $foo->bar(7);
} catch (RuntimeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

/**
 * Using DebugIdentifierAttributeInterface
 */

class Foo31eda25b57e8456fb2b3e8158232b5e5 implements DebugIdentifierAttributeInterface
{
    #[DebugIdentifier]
    protected int $id = 42;

    /**
     * @throws RuntimeException
     */
    public function bar(int $a): string
    {
        /** @var array<ObjectFormatterInterface> $formatters */
        $formatters = [new DebugIdentifierAttributeInterfaceFormatter()];

        $caster = Caster::getInstance();
        $caster = $caster->withCustomObjectFormatterCollection(new ObjectFormatterCollection($formatters));

        $exceptionMessageGenerator = ExceptionMessageGenerator::getInstance()->withCaster($caster);

        throw new RuntimeException($exceptionMessageGenerator->makeFailureInMethodMessage(
            $this,
            new ReflectionMethod(self::class, __FUNCTION__),
            func_get_args(),
        ));
    }
}


$foo = new Foo31eda25b57e8456fb2b3e8158232b5e5();

try {
    $foo->bar(7);
} catch (RuntimeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

```

**Output:**

```
Failure in \Foo1990801ff8324df1b73e323d7fca71a8->bar($a = (int) 7) inside (object) \Foo1990801ff8324df1b73e323d7fca71a8: My ID is: 42
Failure in \Foo31eda25b57e8456fb2b3e8158232b5e5->bar($a = (int) 7) inside (object) \Foo31eda25b57e8456fb2b3e8158232b5e5 {$id = (int) 42}

```

Notice how we now get useful information from the above object, its ID being 42 (and argument `$a` is 7).

You must use `$this` as the argument in the `makeFailureInMethodMessage` call (and **not** `static::class`) for the above to work.

## Exception formatters

### Example 1: Default formatter

Class: `Eboreum\Exceptional\Formatting\DefaultFormatter`

A plain text formatter. Contains line breaks and indentation.

```php
<?php

use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Formatting\DefaultFormatter;

$caster = Caster::getInstance();
$defaultFormatter = new DefaultFormatter($caster);

$throwable = new Exception('foo');

$result = $defaultFormatter->format($throwable);

echo $result;

```

**Output:**

```
\Exception
Message:
    foo
File: /some/file/path/script/misc/readme/formatter/example-1-defaultformatter.php
Line: 14
Code: 0\nStacktrace:\n    #0 /path/to/some/file.php:34: fake_function()\nPrevious: (None)
```

### Example 2: HTML5 `<table>` formatter

Class: `Eboreum\Exceptional\Formatting\HTML5TableFormatter`

Formats the throwable as HTML5 `<table>`.

```php
<?php

use Eboreum\Caster\CharacterEncoding;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Formatting\HTML5TableFormatter;

$caster = Caster::getInstance();
$characterEncoding = new CharacterEncoding('UTF-8');
$html5TableFormatter = new HTML5TableFormatter($caster, $characterEncoding);
$html5TableFormatter = $html5TableFormatter->withIsPrettyPrinting(true);

$throwable = new Exception('foo');

$result = $html5TableFormatter->format($throwable);

echo $result;

```

**Output:**

```
<table>
  <tbody>
    <tr>
      <td colspan="2">
        <h1>\Exception</h1>
      </td>
    </tr>
    <tr>
      <td>Message:</td>
      <td>foo</td>
    </tr>
    <tr>
      <td>File:</td>
      <td>/some/file/path/script/misc/readme/formatter/example-2-html5tableformatter.php</td>
    </tr>
    <tr>
      <td>Line:</td>
      <td>17</td>
    </tr>
    <tr>
      <td>Code:</td>
      <td>0</td>
    </tr>
    <tr>
      <td>Stacktrace:</td>
      <td>
        <pre>#0 /path/to/some/file.php:34: fake_function()</pre>
      </td>
    </tr>
    <tr>
      <td>Previous:</td>
      <td>(None)</td>
    </tr>
  </tbody>
</table>
```

### Example 3: JSON formatter

Class: `Eboreum\Exceptional\Formatting\JSONFormatter`

Formats the throwable as JSON.

```php
<?php

use Eboreum\Caster\CharacterEncoding;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Formatting\JSONFormatter;

$caster = Caster::getInstance();
$characterEncoding = new CharacterEncoding('UTF-8');
$jsonFormatter = new JSONFormatter($caster, $characterEncoding);
$jsonFormatter = $jsonFormatter->withFlags(JSON_PRETTY_PRINT);

$throwable = new Exception('foo');

$result = $jsonFormatter->format($throwable);

echo $result;

```

**Output:**

```
{
    "class": "\\Exception",
    "file": "\/some\/file\/path\/script\/misc\/readme\/formatter\/example-3-jsonformatter.php",
    "line": "17",
    "code": "0",
    "message": "foo",
    "stacktrace": "#0 \/path\/to\/some\/file.php:34: fake_function()"
    "previous": null
}
```

### Example 4: Oneline formatter

Class: `Eboreum\Exceptional\Formatting\OnelineFormatter`

Formats the throwable as string with all its contents on a single line. Great for (improved) output in error logs, which do not allow line breaks.

```php
<?php

use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Formatting\OnelineFormatter;

$caster = Caster::getInstance();
$onelineFormatter = new OnelineFormatter($caster);

$throwable = new Exception('foo');

$result = $onelineFormatter->format($throwable);

echo $result;

```

**Output:**

```
\Exception. Message: foo. File: /some/file/path/script/misc/readme/formatter/example-4-onelineformatter.php. Line: 14. Code: 0. Stacktrace: #0 /path/to/some/file.php:34: fake_function(). Previous: (None)
```

### Example 5: XML formatter

Class: `Eboreum\Exceptional\Formatting\XMLFormatter`

Formats the throwable as XML.

```php
<?php

use Eboreum\Caster\CharacterEncoding;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Formatting\XMLFormatter;

$caster = Caster::getInstance();
$characterEncoding = new CharacterEncoding('UTF-8');
$xmlFormatter = new XMLFormatter($caster, $characterEncoding);
$xmlFormatter = $xmlFormatter->withIsPrettyPrinting(true);

$throwable = new Exception('foo');

$result = $xmlFormatter->format($throwable);

echo $result;

```

**Output:**

```
<?xml version="1.0" encoding="UTF-8"?>
<exception>
  <class>\Exception</class>
  <file>/some/file/path/script/misc/readme/formatter/example-5-xmlformatter.php</file>
  <line>17</line>
  <code>0</code>
  <message>foo</message>
  <stacktrace>#0 /path/to/some/file.php:34: fake_function()</stacktrace>
  <previous/>
</exception>
```

## Test/development requirements

```json
"eboreum/phpunit-with-consecutive-alternative": "^1.0",
"nette/neon": "^3.2",
"phpstan/phpstan": "^2.1.5",
"phpunit/phpunit": "^11.3",
"sebastian/diff": "^6.0",
"slevomat/coding-standard": "8.15.0",
"squizlabs/php_codesniffer": "3.10.2"
```

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

## Branch rules

[`main`](https://github.com/eboreum/exceptional/tree/main) = `2.x` (not a tag)

Previous branches:

- [`1.x`](https://github.com/eboreum/exceptional/tree/1.x)

# Credits

## Authors

- **Kasper Søfren** (kafoso)<br>E-mail: <a href="mailto:soefritz@gmail.com">soefritz@gmail.com</a><br>Homepage: <a href="https://github.com/kafoso">https://github.com/kafoso</a>
- **Carsten Jørgensen** (corex)<br>E-mail: <a href="mailto:dev@corex.dk">dev@corex.dk</a><br>Homepage: <a href="https://github.com/corex">https://github.com/corex</a>
