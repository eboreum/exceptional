<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest;

use Eboreum\Exceptional\ExceptionMessageGenerator;
use ReflectionMethod;

use function func_get_args;

class TestMakeFailureInMethodMessageWorksClassANoNamedArguments
{
    private string $message;

    public function __construct()
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
