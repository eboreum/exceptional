<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\MethodArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenClassExistsButClassVariableDoesNotExist;

class ClassExistsButClassConstantBarDoesNotExistB
{
    public function foo( // @phpstan-ignore-line
        $a = ClassExistsButClassConstantBarDoesNotExistA::BAR // @phpstan-ignore-line
    ): void
    {
    }
}