<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest;

use Eboreum\Exceptional\ExceptionMessageGenerator;

abstract class testMakeFailureInMethodMessageWorks_ANamedClassWhereMethodSignatureChangesBetweenUppermostClassAndParentClasses_C
{
    private string $message;

    public function __construct(int $a, bool $b, string $c)
    {
        $this->message = ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
            $this,
            new \ReflectionMethod(__CLASS__, __FUNCTION__),
            func_get_args(),
        );
    }

    public function __toString(): string
    {
        return $this->message;
    }
}
