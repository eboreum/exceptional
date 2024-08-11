<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest;

abstract class TestMakeUninitializedPropertySafeToTextualIdentifierStringThrowsExceptionWhenPropertiesDoNotExistClassBParent // phpcs:ignore
{
    private string $a; // @phpstan-ignore-line
}
