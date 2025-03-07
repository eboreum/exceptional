<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional;

use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Caster\SensitiveValue;
use Eboreum\Exceptional\AbstractFunctionArgumentDiscloser;
use Eboreum\Exceptional\MethodArgumentDiscloser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use SensitiveParameter;

use function array_key_exists;
use function assert;

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

    public function testGetNormalizedFunctionArgumentValuesWorksWhenParameterIsSensitive(): void
    {
        $object = new class
        {
            public function foo(
                #[SensitiveParameter()]
                int $bar = 42,
                int $baz = 43,
            ): void {
            }
        };

        $reflectionMethod = new ReflectionMethod($object, 'foo');
        $caster = $this->createMock(CasterInterface::class);

        $methodArgumentDiscloser = new MethodArgumentDiscloser($caster, $reflectionMethod, [101, 102]);

        $values = $methodArgumentDiscloser->getNormalizedFunctionArgumentValues();

        $this->assertArrayHasKey(0, $values);
        assert(array_key_exists(0, $values));
        $this->assertIsObject($values[0]);
        $this->assertInstanceOf(SensitiveValue::class, $values[0]);
        $this->assertArrayHasKey(1, $values);
        assert(array_key_exists(1, $values));
        $this->assertSame(102, $values[1]);
    }
}
