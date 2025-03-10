<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest;

use Eboreum\Exceptional\ExceptionMessageGenerator;
use ReflectionMethod;

use function func_get_args;

class TestMakeFailureInMethodMessageWorksWithNonStaticMethodsAClassWithADefaultConstant
{
    public const A_DEFAULT = 99;

    private string $message;

    public function __construct(int $a = self::A_DEFAULT)
    {
        $this->message = ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
            $this,
            new ReflectionMethod(self::class, __FUNCTION__),
            func_get_args(),
        );
    }

    public function __toString(): string
    {
        return $this->message;
    }
}
