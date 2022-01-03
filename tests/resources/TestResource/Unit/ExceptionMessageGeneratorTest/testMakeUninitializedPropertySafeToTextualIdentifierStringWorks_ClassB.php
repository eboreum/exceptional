<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest;

class testMakeUninitializedPropertySafeToTextualIdentifierStringWorks_ClassB extends testMakeUninitializedPropertySafeToTextualIdentifierStringWorks_ClassBParent
{
    private int $b = 42; // @phpstan-ignore-line

    private bool $c; // @phpstan-ignore-line
}
