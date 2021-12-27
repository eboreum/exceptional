<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest;

class testMakeFailureInMethodMessageWorks_ANamedClassWhereMethodSignatureChangesBetweenUppermostClassAndParentClasses_A
    extends testMakeFailureInMethodMessageWorks_ANamedClassWhereMethodSignatureChangesBetweenUppermostClassAndParentClasses_B
{
    public function __construct(int $a)
    {
        parent::__construct($a, true);
    }
}
