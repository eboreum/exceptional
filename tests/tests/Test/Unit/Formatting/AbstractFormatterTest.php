<?php

declare(strict_types = 1);

namespace Test\Unit\Eboreum\Exceptional\Formatting;

use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\Formatting\AbstractFormatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AbstractFormatterTest extends TestCase
{
    public function testBasics(): void
    {
        $caster = $this->_mockCasterInterface();

        $object = new class($caster) extends AbstractFormatter
        {
            public function __construct(CasterInterface $caster)
            {
                $this->caster = $caster;
            }

            public function format(\Throwable $throwable): string
            {
                return "";
            }
        };

        $this->assertSame($caster, $object->getCaster());
        $this->assertSame(0, $object->getPreviousThrowableLevel());
        $this->assertSame(null, $object->getMaximumPreviousDepth());
        $this->assertFalse($object->isProvidingTimestamp());
    }

    /**
     * @dataProvider dataProvider_testMaskStringWorks
     */
    public function testMaskStringWorks(
        string $expected,
        string $maskedString
    ): void
    {
        $caster = $this->_mockCasterInterface();

        $caster
            ->expects($this->exactly(1))
            ->method("maskString")
            ->with()
            ->willReturn($maskedString);

        $object = new class($caster) extends AbstractFormatter
        {
            public function __construct(CasterInterface $caster)
            {
                $this->caster = $caster;
            }

            public function format(\Throwable $throwable): string
            {
                return "";
            }
        };

        $this->assertSame($expected, $object->maskString("foo"));
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    public function dataProvider_testMaskStringWorks(): array
    {
        return [
            [
                "",
                "",
            ],
            [
                "mellon",
                "mellon",
            ],
        ];
    }

    /**
     * @dataProvider dataProvider_testNormalizeFilePathWorks
     */
    public function testNormalizeFilePathWorks(
        string $expected,
        string $filePath
    ): void
    {
        $caster = $this->_mockCasterInterface();

        $object = new class($caster) extends AbstractFormatter
        {
            public function __construct(CasterInterface $caster)
            {
                $this->caster = $caster;
            }

            public function format(\Throwable $throwable): string
            {
                return "";
            }
        };

        $this->assertSame($expected, $object->normalizeFilePath($filePath));
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    public function dataProvider_testNormalizeFilePathWorks(): array
    {
        return [
            [
                "",
                "",
            ],
            [
                "/foo/bar/baz",
                "/foo/bar/baz",
            ],
            [
                "/foo/bar/baz",
                "\\foo\\bar\\baz",
            ],
            [
                "/foo/bar/baz",
                "\\foo/bar\\baz",
            ],
        ];
    }

    public function testWithCasterWorks(): void
    {
        $casterA = $this->_mockCasterInterface();
        $casterB = $this->_mockCasterInterface();
        $casterC = $this->_mockCasterInterface();

        $objectA = new class($casterA) extends AbstractFormatter
        {
            public function __construct(CasterInterface $caster)
            {
                $this->caster = $caster;
            }

            public function format(\Throwable $throwable): string
            {
                return "";
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
        $caster = $this->_mockCasterInterface();

        $objectA = new class($caster) extends AbstractFormatter
        {
            public function __construct(CasterInterface $caster)
            {
                $this->caster = $caster;
            }

            public function format(\Throwable $throwable): string
            {
                return "";
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
        $caster = $this->_mockCasterInterface();

        $objectA = new class($caster) extends AbstractFormatter
        {
            public function __construct(CasterInterface $caster)
            {
                $this->caster = $caster;
            }

            public function format(\Throwable $throwable): string
            {
                return "";
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
        $caster = $this->_mockCasterInterface();

        $objectA = new class($caster) extends AbstractFormatter
        {
            public function __construct(CasterInterface $caster)
            {
                $this->caster = $caster;
            }

            public function format(\Throwable $throwable): string
            {
                return "";
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
        $caster = $this->_mockCasterInterface();

        $object = new class($caster) extends AbstractFormatter
        {
            public function __construct(CasterInterface $caster)
            {
                $this->caster = $caster;
            }

            public function format(\Throwable $throwable): string
            {
                return "";
            }
        };

        try {
            $object->withPreviousThrowableLevel(-1);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode("", [
                        '/',
                        '^',
                        'Failure in class@anonymous\/in\/.+\/%s:\d+-\>withPreviousThrowableLevel\(',
                            '\$previousThrowableLevel = \(int\) \-1',
                        '\) inside \(object\) class@anonymous\/in\/.+\/%s:\d+ \{',
                            '\\\\%s\-\>\$caster = \(object\) \\\\Mock_CasterInterface_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$previousThrowableLevel = \(int\) 0',
                            ', \\\\%s\-\>\$maximumPreviousDepth = \(null\) null',
                            ', \\\\%s\-\>\$isProvidingTimestamp = \(bool\) false',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), "/"),
                    preg_quote(basename(__FILE__), "/"),
                    preg_quote(AbstractFormatter::class, "/"),
                    preg_quote(AbstractFormatter::class, "/"),
                    preg_quote(AbstractFormatter::class, "/"),
                    preg_quote(AbstractFormatter::class, "/"),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode("", [
                        '/',
                        '^',
                        'Expects argument \$previousThrowableLevel to be \<\= 0, but it is not\.',
                        ' Found: \(int\) \-1',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), "/"),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(is_null($currentException));

            return;
        }

        $this->fail("Exception was never thrown.");
    }

    /**
     * @dataProvider dataProvider_testSplitTextLinesToArrayWorks
     * @param array<int, string> $expected
     */
    public function testSplitTextLinesToArrayWorks(
        array $expected,
        string $str
    ): void
    {
        $this->assertSame($expected, AbstractFormatter::splitTextLinesToArray($str));
    }

    /**
     * @return array<int, array{0: array<int, string>, 1: string}>
     */
    public function dataProvider_testSplitTextLinesToArrayWorks(): array
    {
        return [
            [
                [
                    "",
                ],
                "",
            ],
            [
                [
                    "foo",
                ],
                "foo",
            ],
            [
                [
                    "foo",
                    "bar",
                ],
                "foo\nbar",
            ],
            [
                [
                    "foo",
                    "bar",
                    "baz",
                    "bim",
                    "",
                    "bum",
                ],
                "foo\nbar\rbaz\r\nbim\n\rbum",
            ],
        ];
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