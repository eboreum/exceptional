<?php
// phpcs:ignoreFile

declare(strict_types=1); // README.md.remove

use Eboreum\Exceptional\ExceptionMessageGenerator;

require_once dirname(__DIR__, 3) . '/bootstrap.php'; // README.md.remove

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
