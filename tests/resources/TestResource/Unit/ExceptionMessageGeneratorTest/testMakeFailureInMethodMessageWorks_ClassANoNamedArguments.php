<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest;

use Eboreum\Exceptional\ExceptionMessageGenerator;

class testMakeFailureInMethodMessageWorks_ClassANoNamedArguments
{
    private string $message;

    public function __construct()
    {
        $this->message = ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
            $this,
            new \ReflectionMethod(static::class, __FUNCTION__),
            func_get_args(),
        );
    }

    public function __toString(): string
    {
        return $this->message;
    }
}
