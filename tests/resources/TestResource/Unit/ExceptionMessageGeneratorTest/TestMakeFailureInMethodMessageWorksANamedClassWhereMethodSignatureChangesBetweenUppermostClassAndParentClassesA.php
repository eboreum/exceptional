<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest;

class TestMakeFailureInMethodMessageWorksANamedClassWhereMethodSignatureChangesBetweenUppermostClassAndParentClassesA extends TestMakeFailureInMethodMessageWorksANamedClassWhereMethodSignatureChangesBetweenUppermostClassAndParentClassesB // phpcs:ignore
{
    public function __construct(int $a)
    {
        parent::__construct($a, true);
    }
}
