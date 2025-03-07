<?php

declare(strict_types=1);

namespace Test\Integration\Eboreum\Exceptional;

use Eboreum\Caster\Caster;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\ExceptionMessageGenerator;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use SensitiveParameter;

use function preg_quote;
use function sprintf;

#[CoversNothing()]
class SensitiveParameterTest extends TestCase
{
    public function testCasterWillRedactSensitiveParameter(): void
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

        $message = ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
            $object,
            $reflectionMethod,
            [101, 102],
        );

        $this->assertMatchesRegularExpression(
            sprintf(
                '/^Failure in %s->foo\(\$bar = %s, \$baz = %s\) inside \(object\) %1$s$/D',
                preg_quote(Caster::makeNormalizedClassName($reflectionMethod->getDeclaringClass()), '/'),
                preg_quote(CasterInterface::SENSITIVE_MESSAGE_DEFAULT, '/'),
                preg_quote(Caster::getInstance()->castTyped(102), '/'),
            ),
            $message
        );
    }
}
