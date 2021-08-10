<?php

declare(strict_types = 1);

namespace Test\Unit\Eboreum\Exceptional;

use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\AbstractFunctionArgumentDiscloser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AbstractFunctionArgumentDiscloserTest extends TestCase
{
    public function testBasics(): void
    {
        $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
        $caster = $this->_mockCasterInterface();

        $object = new class($caster, $reflectionMethod, []) extends AbstractFunctionArgumentDiscloser
        {
            public function __construct(
                CasterInterface $caster,
                \ReflectionMethod $reflectionMethod,
                array $functionArgumentValues
            )
            {
                $this->caster = $caster;
                $this->reflectionFunction = $reflectionMethod;
                $this->functionArgumentValues = $functionArgumentValues;
            }

            public static function getDefaultValueConstantRegex(): string
            {
                return '';
            }
        };

        $this->assertSame($reflectionMethod, $object->getReflectionFunction());
    }

    /**
     * @return CasterInterface&MockObject
     */
    private function _mockCasterInterface(): CasterInterface
    {
        return $this
            ->getMockBuilder(CasterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
