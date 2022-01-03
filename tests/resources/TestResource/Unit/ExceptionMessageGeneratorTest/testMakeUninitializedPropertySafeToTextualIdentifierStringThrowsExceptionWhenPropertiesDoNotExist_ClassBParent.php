<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest;

abstract class testMakeUninitializedPropertySafeToTextualIdentifierStringThrowsExceptionWhenPropertiesDoNotExist_ClassBParent
{
    private string $a; // @phpstan-ignore-line
}
