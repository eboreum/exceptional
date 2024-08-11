<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\MethodArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenClassExistsButClassVariableDoesNotExist; // phpcs:ignore

class ClassExistsButClassConstantBarDoesNotExistB
{
    // @phpstan-ignore-next-line
    public function foo( // phpcs:ignore
        $a = ClassExistsButClassConstantBarDoesNotExistA::BAR // @phpstan-ignore-line
    ): void {
    }
}
