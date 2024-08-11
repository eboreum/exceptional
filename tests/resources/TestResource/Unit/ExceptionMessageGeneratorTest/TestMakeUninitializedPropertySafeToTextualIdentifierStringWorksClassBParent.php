<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest;

abstract class TestMakeUninitializedPropertySafeToTextualIdentifierStringWorksClassBParent
{
    private string $a; // @phpstan-ignore-line
}
