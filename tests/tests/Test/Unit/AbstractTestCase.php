<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional;

use Eboreum\PhpunitWithConsecutiveAlternative\MethodCallExpectation;
use Eboreum\PhpunitWithConsecutiveAlternative\WillHandleConsecutiveCalls;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    /**
     * @param non-empty-string $methodName
     */
    final public static function expectConsecutiveCalls(
        MockObject $object,
        string $methodName,
        MethodCallExpectation ...$methodCallExpectations,
    ): void {
        (new WillHandleConsecutiveCalls())->expectConsecutiveCalls($object, $methodName, ...$methodCallExpectations);
    }
}
