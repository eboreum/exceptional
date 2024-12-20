<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional\Formatting;

use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\Formatting\AbstractFormatter;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Throwable;

use function assert;
use function basename;
use function implode;
use function is_object;
use function preg_quote;
use function sprintf;

#[CoversClass(AbstractFormatter::class)]
class AbstractFormatterTest extends TestCase
{
    /**
     * @return array<int, array{0: string, 1: string}>
     */
    public static function providerTestMaskStringWorks(): array
    {
        return [
            [
                '',
                '',
            ],
            [
                'mellon',
                'mellon',
            ],
        ];
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    public static function providerTestNormalizeFilePathWorks(): array
    {
        return [
            [
                '',
                '',
            ],
            [
                '/foo/bar/baz',
                '/foo/bar/baz',
            ],
            [
                '/foo/bar/baz',
                '\\foo\\bar\\baz',
            ],
            [
                '/foo/bar/baz',
                '\\foo/bar\\baz',
            ],
        ];
    }

    /**
     * @return array<int, array{0: array<int, string>, 1: string}>
     */
    public static function providerTestSplitTextLinesToArrayWorks(): array
    {
        return [
            [
                [''],
                '',
            ],
            [
                ['foo'],
                'foo',
            ],
            [
                [
                    'foo',
                    'bar',
                ],
                "foo\nbar",
            ],
            [
                [
                    'foo',
                    'bar',
                    'baz',
                    'bim',
                    '',
                    'bum',
                ],
                "foo\nbar\rbaz\r\nbim\n\rbum",
            ],
        ];
    }

    public function testBasics(): void
    {
        $caster = $this->createMock(CasterInterface::class);

        $object = new class ($caster) extends AbstractFormatter
        {
            public function __construct(CasterInterface $caster)
            {
                $this->caster = $caster;
            }

            public function format(Throwable $throwable): string
            {
                return '';
            }
        };

        $this->assertSame($caster, $object->getCaster());
        $this->assertSame(0, $object->getPreviousThrowableLevel());
        $this->assertSame(null, $object->getMaximumPreviousDepth());
        $this->assertFalse($object->isProvidingTimestamp());
    }

    #[DataProvider('providerTestMaskStringWorks')]
    public function testMaskStringWorks(string $expected, string $maskedString): void
    {
        $caster = $this->createMock(CasterInterface::class);

        $caster
            ->expects($this->exactly(1))
            ->method('maskString')
            ->with()
            ->willReturn($maskedString);

        $object = new class ($caster) extends AbstractFormatter
        {
            public function __construct(CasterInterface $caster)
            {
                $this->caster = $caster;
            }

            public function format(Throwable $throwable): string
            {
                return '';
            }
        };

        $this->assertSame($expected, $object->maskString('foo'));
    }

    #[DataProvider('providerTestNormalizeFilePathWorks')]
    public function testNormalizeFilePathWorks(string $expected, string $filePath): void
    {
        $caster = $this->createMock(CasterInterface::class);

        $object = new class ($caster) extends AbstractFormatter
        {
            public function __construct(CasterInterface $caster)
            {
                $this->caster = $caster;
            }

            public function format(Throwable $throwable): string
            {
                return '';
            }
        };

        $this->assertSame($expected, $object->normalizeFilePath($filePath));
    }

    public function testWithCasterWorks(): void
    {
        $casterA = $this->createMock(CasterInterface::class);
        $casterB = $this->createMock(CasterInterface::class);
        $casterC = $this->createMock(CasterInterface::class);

        $objectA = new class ($casterA) extends AbstractFormatter
        {
            public function __construct(CasterInterface $caster)
            {
                $this->caster = $caster;
            }

            public function format(Throwable $throwable): string
            {
                return '';
            }
        };

        $objectB = $objectA->withCaster($casterB);
        $objectC = $objectA->withCaster($casterC);

        $this->assertNotSame($objectA, $objectB);
        $this->assertNotSame($objectA, $objectC);
        $this->assertNotSame($objectB, $objectC);
        $this->assertSame($casterA, $objectA->getCaster());
        $this->assertSame($casterB, $objectB->getCaster());
        $this->assertSame($casterC, $objectC->getCaster());
    }

    public function testWithIsProvidingTimestampWorks(): void
    {
        $caster = $this->createMock(CasterInterface::class);

        $objectA = new class ($caster) extends AbstractFormatter
        {
            public function __construct(CasterInterface $caster)
            {
                $this->caster = $caster;
            }

            public function format(Throwable $throwable): string
            {
                return '';
            }
        };

        $objectB = $objectA->withIsProvidingTimestamp(false);
        $objectC = $objectA->withIsProvidingTimestamp(true);

        $this->assertNotSame($objectA, $objectB);
        $this->assertNotSame($objectA, $objectC);
        $this->assertNotSame($objectB, $objectC);
        $this->assertSame(false, $objectA->isProvidingTimestamp());
        $this->assertSame(false, $objectB->isProvidingTimestamp());
        $this->assertSame(true, $objectC->isProvidingTimestamp());
    }

    public function testWithMaximumPreviousDepthWorks(): void
    {
        $caster = $this->createMock(CasterInterface::class);

        $objectA = new class ($caster) extends AbstractFormatter
        {
            public function __construct(CasterInterface $caster)
            {
                $this->caster = $caster;
            }

            public function format(Throwable $throwable): string
            {
                return '';
            }
        };

        $objectB = $objectA->withMaximumPreviousDepth(null);
        $objectC = $objectA->withMaximumPreviousDepth(42);

        $this->assertNotSame($objectA, $objectB);
        $this->assertNotSame($objectA, $objectC);
        $this->assertNotSame($objectB, $objectC);
        $this->assertSame(null, $objectA->getMaximumPreviousDepth());
        $this->assertSame(null, $objectB->getMaximumPreviousDepth());
        $this->assertSame(42, $objectC->getMaximumPreviousDepth());
    }

    public function testWithPreviousThrowableLevelWorks(): void
    {
        $caster = $this->createMock(CasterInterface::class);

        $objectA = new class ($caster) extends AbstractFormatter
        {
            public function __construct(CasterInterface $caster)
            {
                $this->caster = $caster;
            }

            public function format(Throwable $throwable): string
            {
                return '';
            }
        };

        $objectB = $objectA->withPreviousThrowableLevel(0);
        $objectC = $objectA->withPreviousThrowableLevel(1);

        $this->assertNotSame($objectA, $objectB);
        $this->assertNotSame($objectA, $objectC);
        $this->assertNotSame($objectB, $objectC);
        $this->assertSame(0, $objectA->getPreviousThrowableLevel());
        $this->assertSame(0, $objectB->getPreviousThrowableLevel());
        $this->assertSame(1, $objectC->getPreviousThrowableLevel());
    }

    public function testWithPreviousThrowableLevelThrowsExceptionWhenArgumentPreviousThrowableLevelIsBelowZero(): void
    {
        $caster = $this->createMock(CasterInterface::class);

        $object = new class ($caster) extends AbstractFormatter
        {
            public function __construct(CasterInterface $caster)
            {
                $this->caster = $caster;
            }

            public function format(Throwable $throwable): string
            {
                return '';
            }
        };

        try {
            $object->withPreviousThrowableLevel(-1);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>withPreviousThrowableLevel\(',
                            '\$previousThrowableLevel = \(int\) \-1',
                        '\) inside \(object\) \\\\%1$s@anonymous\/in\/.+\/%s:\d+ \{',
                            '\\\\%1$s\-\>\$caster = \(object\) \\\\MockObject_CasterInterface_[0-9a-f]{8}',
                            ', \\\\%1$s\-\>\$previousThrowableLevel = \(int\) 0',
                            ', \\\\%1$s\-\>\$maximumPreviousDepth = \(null\) null',
                            ', \\\\%1$s\-\>\$isProvidingTimestamp = \(bool\) false',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Expects argument \$previousThrowableLevel to be \<\= 0, but it is not\.',
                    ' Found: \(int\) \-1',
                    '$',
                    '/',
                ]),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    /**
     * @param array<int, string> $expected
     */
    #[DataProvider('providerTestSplitTextLinesToArrayWorks')]
    public function testSplitTextLinesToArrayWorks(array $expected, string $str): void
    {
        $this->assertSame($expected, AbstractFormatter::splitTextLinesToArray($str));
    }
}
