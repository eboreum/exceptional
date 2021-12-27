<?php

declare(strict_types = 1); // README.md.remove

use Eboreum\Exceptional\ExceptionMessageGenerator;

require_once dirname(__DIR__, 3) . "/bootstrap.php"; // README.md.remove

class Foo377464ece90d4b918254101d596d90a8
{
    /**
     * @throws \RuntimeException
     */
    public function bar(int $a, bool $b, ?string $c = null): string
    {
        throw new \RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
            $this,
            new \ReflectionMethod(__CLASS__, __FUNCTION__),
            func_get_args(),
        ));
    }
};

$foo = new Foo377464ece90d4b918254101d596d90a8;

try {
    $foo->bar(42, true);
} catch (\RuntimeException $e) {
    echo $e->getMessage() . PHP_EOL;
}