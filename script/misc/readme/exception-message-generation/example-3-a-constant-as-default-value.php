<?php

declare(strict_types = 1); // README.md.remove

use Eboreum\Exceptional\ExceptionMessageGenerator;

require_once dirname(__DIR__, 3) . "/bootstrap.php"; // README.md.remove

define("GLOBAL_CONSTANT_25b105757d32443188cca9c7646ccfe6", "Lorem");

class Fooaea91664ed3d4467aeb2dfabb2623b53
{
    const SOME_PARENT_CONSTANT = 42;
}

class Fooc261bae9da674d679de77a943ae57779 extends Fooaea91664ed3d4467aeb2dfabb2623b53
{
    const SOME_CONSTANT = 3.14;

    /**
     * @throws \RuntimeException
     */
    public function bar(
        float $a = self::SOME_CONSTANT,
        int $b = self::SOME_PARENT_CONSTANT,
        string $c = GLOBAL_CONSTANT_25b105757d32443188cca9c7646ccfe6
    ): string
    {
        throw new \RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
            $this,
            new \ReflectionMethod(__CLASS__, __FUNCTION__),
            func_get_args(),
        ));
    }
};

$foo = new Fooc261bae9da674d679de77a943ae57779;

try {
    $foo->bar(42);
} catch (\RuntimeException $e) {
    echo $e->getMessage() . PHP_EOL;
}