<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional;

use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\AbstractFunctionArgumentDiscloser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

#[CoversClass(AbstractFunctionArgumentDiscloser::class)]
class AbstractFunctionArgumentDiscloserTest extends TestCase
{
    public function testBasics(): void
    {
        $reflectionMethod = new ReflectionMethod($this, __FUNCTION__);
        $caster = $this->createMock(CasterInterface::class);

        $object = new class ($caster, $reflectionMethod, []) extends AbstractFunctionArgumentDiscloser
        {
            public static function getDefaultValueConstantRegex(): string
            {
                return '';
            }

            /**
             * @param array<mixed> $functionArgumentValues
             */
            public function __construct(
                CasterInterface $caster,
                ReflectionMethod $reflectionMethod,
                array $functionArgumentValues
            ) {
                $this->caster = $caster;
                $this->reflectionFunction = $reflectionMethod;
                $this->functionArgumentValues = $functionArgumentValues;
            }
        };

        $this->assertSame($reflectionMethod, $object->getReflectionFunction());
    }
}
