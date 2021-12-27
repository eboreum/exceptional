<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest;

abstract class testMakeFailureInMethodMessageWorks_ANamedClassWhereMethodSignatureChangesBetweenUppermostClassAndParentClasses_B
    extends testMakeFailureInMethodMessageWorks_ANamedClassWhereMethodSignatureChangesBetweenUppermostClassAndParentClasses_C
{
    public function __construct(int $a, bool $b)
    {
        parent::__construct($a, $b, "foo");
    }
}
