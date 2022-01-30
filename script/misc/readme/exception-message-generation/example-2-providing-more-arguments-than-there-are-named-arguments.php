<?php

declare(strict_types = 1); // README.md.remove

use Eboreum\Exceptional\ExceptionMessageGenerator;

require_once dirname(__DIR__, 3) . '/bootstrap.php'; // README.md.remove

class Foo1ff07b0e563e4efbb5a5280f7fe412d8
{
    /**
     * @throws \RuntimeException
     */
    public function bar(int $a, bool $b): string
    {
        throw new \RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
            $this,
            new \ReflectionMethod(self::class, __FUNCTION__),
            func_get_args(),
        ));
    }
};

$foo = new Foo1ff07b0e563e4efbb5a5280f7fe412d8;

try {
    $foo->bar(42, true, null, 'hello');
} catch (\RuntimeException $e) {
    echo $e->getMessage() . PHP_EOL;
}