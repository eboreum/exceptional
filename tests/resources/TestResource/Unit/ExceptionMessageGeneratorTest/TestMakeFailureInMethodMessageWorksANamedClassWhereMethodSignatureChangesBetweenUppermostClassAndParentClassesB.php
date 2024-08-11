<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest;

abstract class TestMakeFailureInMethodMessageWorksANamedClassWhereMethodSignatureChangesBetweenUppermostClassAndParentClassesB extends TestMakeFailureInMethodMessageWorksANamedClassWhereMethodSignatureChangesBetweenUppermostClassAndParentClassesC // phpcs:ignore
{
    public function __construct(int $a, bool $b)
    {
        parent::__construct($a, $b, 'foo');
    }
}
