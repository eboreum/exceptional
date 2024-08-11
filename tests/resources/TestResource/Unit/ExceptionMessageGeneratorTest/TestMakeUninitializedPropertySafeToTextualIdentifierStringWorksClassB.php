<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest;

class TestMakeUninitializedPropertySafeToTextualIdentifierStringWorksClassB extends TestMakeUninitializedPropertySafeToTextualIdentifierStringWorksClassBParent // phpcs:ignore
{
    private int $b = 42; // @phpstan-ignore-line

    private bool $c; // @phpstan-ignore-line
}
