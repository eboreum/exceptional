<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional\Formatting;

use Eboreum\Caster\CharacterEncoding;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\Formatting\AbstractFormatter;
use Eboreum\Exceptional\Formatting\JSONFormatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class JSONFormatterTest extends TestCase
{
    public function testBasics(): void
    {
        $caster = $this->mockCasterInterface();
        $characterEncoding = $this->mockCharacterEncoding();

        $jsonFormatter = new JSONFormatter($caster, $characterEncoding);

        $this->assertSame($characterEncoding, $jsonFormatter->getCharacterEncoding());
        $this->assertSame(0, $jsonFormatter->getPreviousThrowableLevel());
        $this->assertSame($caster, $jsonFormatter->getCaster());
    }

    /**
     * @dataProvider dataProvider_testFormatWorks
     */
    public function testFormatWorks(
        string $expectedJSONRegex,
        JSONFormatter $jsonFormatter,
        \Throwable $throwable
    ): void {
        $this->assertMatchesRegularExpression($expectedJSONRegex, $jsonFormatter->format($throwable));
    }

    /**
     * @return array<array{0: string, 1: JSONFormatter, 2: \Exception}>
     */
    public function dataProvider_testFormatWorks(): array
    {
        return [
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\{',
                            '"class"\:"\\\\\\\\Exception"',
                            ',"file"\:".+\/[^\/]+\/%s"',
                            ',"line"\:"\d+"',
                            ',"code"\:"0"',
                            ',"message"\:"foo"',
                            ',"stacktrace"\:"#0 Lorem"',
                            ',"previous"\:null',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                (function () {
                    $caster = $this->mockCasterInterface();
                    $characterEncoding = $this->mockCharacterEncoding();

                    $caster
                        ->expects($this->exactly(2))
                        ->method('maskString')
                        ->withConsecutive(
                            ['foo'],
                            [
                                $this->callback(static function (string $v) {
                                    return (1 === preg_match('/^#0 /', $v));
                                }),
                            ],
                        )
                        ->willReturnOnConsecutiveCalls(
                            'foo',
                            '#0 Lorem',
                        );

                    return new JSONFormatter($caster, $characterEncoding);
                })(),
                new \Exception('foo'),
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\{',
                            '"class"\:"\\\\\\\\Exception"',
                            ',"time"\:"\d{4}\-\d{2}\-\d{2}T\d{2}\:\d{2}\:\d{2}[\+\-]\d{2}\:\d{2}"',
                            ',"file"\:".+\/[^\/]+\/%s"',
                            ',"line"\:"\d+"',
                            ',"code"\:"0"',
                            ',"message"\:"foo"',
                            ',"stacktrace"\:"#0 Lorem"',
                            ',"previous"\:null',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                (function () {
                    $caster = $this->mockCasterInterface();
                    $characterEncoding = $this->mockCharacterEncoding();

                    $caster
                        ->expects($this->exactly(2))
                        ->method('maskString')
                        ->withConsecutive(
                            ['foo'],
                            [
                                $this->callback(static function (string $v) {
                                    return (1 === preg_match('/^#0 /', $v));
                                }),
                            ],
                        )
                        ->willReturnOnConsecutiveCalls(
                            'foo',
                            '#0 Lorem',
                        );

                    $jsonFormatter = new JSONFormatter($caster, $characterEncoding);

                    /**
                     * @var JSONFormatter
                     */
                    $jsonFormatter = $jsonFormatter->withIsProvidingTimestamp(true);

                    return $jsonFormatter;
                })(),
                new \Exception('foo'),
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\{',
                            '"class"\:"\\\\\\\\Exception"',
                            ',"file"\:".+\/[^\/]+\/%s"',
                            ',"line"\:"\d+"',
                            ',"code"\:"0"',
                            ',"message"\:"foo"',
                            ',"stacktrace"\:"#0 Lorem"',
                            ',"previous"\:\{',
                                '"class":"\\\\\\\\RuntimeException"',
                                ',"file"\:".+\/[^\/]+\/%s"',
                                ',"line"\:"\d+"',
                                ',"code"\:"1"',
                                ',"message"\:"bar"',
                                ',"stacktrace"\:"#0 Ipsum"',
                                ',"previous"\:\{',
                                    '"class":"\\\\\\\\LogicException"',
                                    ',"file"\:".+\/[^\/]+\/%s"',
                                    ',"line"\:"\d+"',
                                    ',"code"\:"2"',
                                    ',"message"\:"baz"',
                                    ',"stacktrace"\:"#0 Dolor"',
                                    ',"previous"\:null',
                                '\}',
                            '\}',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(basename(__FILE__), '/'),
                ),
                (function () {
                    $caster = $this->mockCasterInterface();
                    $characterEncoding = $this->mockCharacterEncoding();

                    $caster
                        ->expects($this->exactly(6))
                        ->method('maskString')
                        ->withConsecutive(
                            ['foo'],
                            [
                                $this->callback(static function (string $v) {
                                    return (1 === preg_match('/^#0 /', $v));
                                }),
                            ],
                            ['bar'],
                            [
                                $this->callback(static function (string $v) {
                                    return (1 === preg_match('/^#0 /', $v));
                                }),
                            ],
                            ['baz'],
                            [
                                $this->callback(static function (string $v) {
                                    return (1 === preg_match('/^#0 /', $v));
                                }),
                            ],
                        )
                        ->willReturnOnConsecutiveCalls(
                            'foo',
                            '#0 Lorem',
                            'bar',
                            '#0 Ipsum',
                            'baz',
                            '#0 Dolor',
                        );

                    return new JSONFormatter($caster, $characterEncoding);
                })(),
                (static function () {
                    $baz = new \LogicException('baz', 2);
                    $bar = new \RuntimeException('bar', 1, $baz);

                    return new \Exception('foo', 0, $bar);
                })(),
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\{',
                            '"class"\:"\\\\\\\\Exception"',
                            ',"file"\:".+\/[^\/]+\/%s"',
                            ',"line"\:"\d+"',
                            ',"code"\:"0"',
                            ',"message"\:"foo"',
                            ',"stacktrace"\:"#0 Lorem"',
                            ',"previous"\:\{',
                                '"class":"\\\\\\\\RuntimeException"',
                                ',"file"\:".+\/[^\/]+\/%s"',
                                ',"line"\:"\d+"',
                                ',"code"\:"1"',
                                ',"message"\:"bar"',
                                ',"stacktrace"\:"#0 Ipsum"',
                                ',"previous"\:"2 more \(omitted\)"',
                            '\}',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(basename(__FILE__), '/'),
                ),
                (function () {
                    $caster = $this->mockCasterInterface();
                    $characterEncoding = $this->mockCharacterEncoding();

                    $caster
                        ->expects($this->exactly(4))
                        ->method('maskString')
                        ->withConsecutive(
                            ['foo'],
                            [
                                $this->callback(static function (string $v) {
                                    return (1 === preg_match('/^#0 /', $v));
                                }),
                            ],
                            ['bar'],
                            [
                                $this->callback(static function (string $v) {
                                    return (1 === preg_match('/^#0 /', $v));
                                }),
                            ],
                        )
                        ->willReturnOnConsecutiveCalls(
                            'foo',
                            '#0 Lorem',
                            'bar',
                            '#0 Ipsum',
                        );

                    $jsonFormatter = new JSONFormatter($caster, $characterEncoding);

                    /**
                     * @var JSONFormatter
                     */
                    $jsonFormatter = $jsonFormatter->withMaximumPreviousDepth(1);

                    return $jsonFormatter;
                })(),
                (static function () {
                    $bim = new \LogicException('bim', 3);
                    $baz = new \LogicException('baz', 2, $bim);
                    $bar = new \RuntimeException('bar', 1, $baz);

                    return new \Exception('foo', 0, $bar);
                })(),
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\{',
                            '"class"\:"\\\\\\\\Exception"',
                            ',"file"\:".+\/[^\/]+\/%s"',
                            ',"line"\:"\d+"',
                            ',"code"\:"0"',
                            ',"message"\:"æøå"',
                            ',"stacktrace"\:"#0 Lorem"',
                            ',"previous"\:null',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(basename(__FILE__), '/'),
                ),
                (function () {
                    $caster = $this->mockCasterInterface();
                    $characterEncoding = $this->mockCharacterEncoding();

                    $caster
                        ->expects($this->exactly(2))
                        ->method('maskString')
                        ->withConsecutive(
                            ['æøå'],
                            [
                                $this->callback(static function (string $v) {
                                    return (1 === preg_match('/^#0 /', $v));
                                }),
                            ],
                        )
                        ->willReturnOnConsecutiveCalls(
                            'æøå',
                            '#0 Lorem',
                        );

                    $jsonFormatter = new JSONFormatter($caster, $characterEncoding);

                    /**
                     * @var JSONFormatter
                     */
                    $jsonFormatter = $jsonFormatter->withFlags(JSON_UNESCAPED_UNICODE);

                    return $jsonFormatter;
                })(),
                new \Exception('æøå'),
            ],
        ];
    }

    public function testFormatThrowsExceptionWhenMaximumChildDepthIsReached(): void
    {
        $bar = new \RuntimeException('bar', 1);
        $foo = new \Exception('foo', 0, $bar);

        $caster = $this->mockCasterInterface();
        $characterEncoding = $this->mockCharacterEncoding();

        $caster
            ->expects($this->exactly(2))
            ->method('maskString')
            ->withConsecutive(
                ['foo'],
                [
                    $this->callback(static function (string $v) {
                        return (1 === preg_match('/^#0 /', $v));
                    }),
                ],
            )
            ->willReturnOnConsecutiveCalls(
                'foo',
                '#0 Lorem',
            );

        $jsonFormatter = new JSONFormatter($caster, $characterEncoding);
        $jsonFormatter = $jsonFormatter->withDepth(1);

        try {
            $jsonFormatter->format($foo);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>format\(',
                            '\$throwable = \(object\) \\\\Exception \{.+\}',
                        '\) inside \(object\) \\\\%s \{',
                            '\$characterEncoding = \(object\) \\\\Mock_CharacterEncoding_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$caster = \(object\) \\\\Mock_CasterInterface_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$previousThrowableLevel = \(int\) 0',
                            ', \\\\%s\-\>\$maximumPreviousDepth = \(null\) null',
                            ', \\\\%s\-\>\$isProvidingTimestamp = \(bool\) false',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(JSONFormatter::class, '/'),
                    preg_quote(JSONFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Maximum JSON depth of 1 was reached; cannot produce JSON',
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

    public function testFormatThrowsExceptionWhenJSONDepthIsReached(): void
    {
        $bar = new \RuntimeException('bar', 1);
        $foo = new \Exception('foo', 0, $bar);

        $caster = $this->mockCasterInterface();
        $characterEncoding = $this->mockCharacterEncoding();

        $caster
            ->expects($this->exactly(2))
            ->method('maskString')
            ->withConsecutive(
                ['foo'],
                [
                    $this->callback(static function (string $v) {
                        return (1 === preg_match('/^#0 /', $v));
                    }),
                ],
            )
            ->willReturnOnConsecutiveCalls(
                'foo',
                '#0 Lorem',
            );

        $jsonFormatter = new JSONFormatter($caster, $characterEncoding);
        $jsonFormatter = $jsonFormatter->withDepth(1);

        try {
            $jsonFormatter->format($foo);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>format\(',
                            '\$throwable = \(object\) \\\\Exception \{.+\}',
                        '\) inside \(object\) \\\\%s \{',
                            '\$characterEncoding = \(object\) \\\\Mock_CharacterEncoding_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$caster = \(object\) \\\\Mock_CasterInterface_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$previousThrowableLevel = \(int\) 0',
                            ', \\\\%s\-\>\$maximumPreviousDepth = \(null\) null',
                            ', \\\\%s\-\>\$isProvidingTimestamp = \(bool\) false',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(JSONFormatter::class, '/'),
                    preg_quote(JSONFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Maximum JSON depth of 1 was reached; cannot produce JSON',
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

    public function testFormatThrowsExceptionWhenjson_encodeFailsByReturningFalse(): void
    {
        $caster = $this->mockCasterInterface();
        $characterEncoding = $this->mockCharacterEncoding();

        $jsonFormatter = new class ($caster, $characterEncoding) extends JSONFormatter
        {
            /**
             * @override
             */
            protected function formatInner(\Throwable $throwable, JSONFormatter $topLevelJSONFormatter): \stdClass
            {
                return (object)[
                    'too' => [
                        'many' => [
                            'levels' => [],
                        ],
                    ],
                ];
            }
        };

        $jsonFormatter = $jsonFormatter->withDepth(2);

        $exception = new \Exception();

        try {
            $jsonFormatter->format($exception);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>format\(',
                            '\$throwable = \(object\) \\\\Exception \{.+\}',
                        '\) inside \(object\) class@anonymous\/in\/.+\/%s:\d+ \{',
                            '\\\\%s\-\>\$characterEncoding = \(object\) \\\\Mock_CharacterEncoding_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$caster = \(object\) \\\\Mock_CasterInterface_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$previousThrowableLevel = \(int\) 0',
                            ', \\\\%s\-\>\$maximumPreviousDepth = \(null\) null',
                            ', \\\\%s\-\>\$isProvidingTimestamp = \(bool\) false',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(JSONFormatter::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(JSONFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'JSON encoding failed\: \(JSON_ERROR_DEPTH\) Maximum stack depth exceeded',
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

    public function testFormatThrowsExceptionWhenjson_encodeFailsByThrowingException(): void
    {
        $caster = $this->mockCasterInterface();
        $characterEncoding = $this->mockCharacterEncoding();

        $jsonFormatter = new class ($caster, $characterEncoding) extends JSONFormatter
        {
            /**
             * @override
             */
            protected function formatInner(\Throwable $throwable, JSONFormatter $topLevelJSONFormatter): \stdClass
            {
                return (object)[
                    'too' => [
                        'many' => [
                            'levels' => [],
                        ],
                    ],
                ];
            }
        };

        $jsonFormatter = $jsonFormatter->withFlags(JSON_THROW_ON_ERROR);
        $jsonFormatter = $jsonFormatter->withDepth(2);

        $exception = new \Exception();

        try {
            $jsonFormatter->format($exception);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>format\(',
                            '\$throwable = \(object\) \\\\Exception \{.+\}',
                        '\) inside \(object\) class@anonymous\/in\/.+\/%s:\d+ \{',
                            '\\\\%s\-\>\$characterEncoding = \(object\) \\\\Mock_CharacterEncoding_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$caster = \(object\) \\\\Mock_CasterInterface_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$previousThrowableLevel = \(int\) 0',
                            ', \\\\%s\-\>\$maximumPreviousDepth = \(null\) null',
                            ', \\\\%s\-\>\$isProvidingTimestamp = \(bool\) false',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(JSONFormatter::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(JSONFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Failure when calling\: json_encode\(, , \)',
                    '$',
                    '/',
                ]),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame('JsonException', get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Maximum stack depth exceeded',
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

    public function testWithDepthWorks(): void
    {
        $caster = $this->mockCasterInterface();
        $characterEncoding = $this->mockCharacterEncoding();

        $jsonFormatterA = new JSONFormatter($caster, $characterEncoding);

        $jsonFormatterB = $jsonFormatterA->withDepth($jsonFormatterA->getDepth());
        $jsonFormatterC = $jsonFormatterA->withDepth(1);

        $this->assertNotSame($jsonFormatterA, $jsonFormatterB);
        $this->assertNotSame($jsonFormatterA, $jsonFormatterC);
        $this->assertNotSame($jsonFormatterB, $jsonFormatterC);
        $this->assertSame(512, $jsonFormatterA->getDepth());
        $this->assertSame(512, $jsonFormatterB->getDepth());
        $this->assertSame(1, $jsonFormatterC->getDepth());
    }

    public function testWithDepthThrowsExceptionWhenArgumentDepthIsOutOfBounds(): void
    {
        $caster = $this->mockCasterInterface();
        $characterEncoding = $this->mockCharacterEncoding();

        $jsonFormatter = new JSONFormatter($caster, $characterEncoding);

        try {
            $jsonFormatter->withDepth(0);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>withDepth\(',
                            '\$depth = \(int\) 0',
                        '\) inside \(object\) \\\\%s \{',
                            '\$characterEncoding = \(object\) \\\\Mock_CharacterEncoding_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$caster = \(object\) \\\\Mock_CasterInterface_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$previousThrowableLevel = \(int\) 0',
                            ', \\\\%s\-\>\$maximumPreviousDepth = \(null\) null',
                            ', \\\\%s\-\>\$isProvidingTimestamp = \(bool\) false',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(JSONFormatter::class, '/'),
                    preg_quote(JSONFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Expects argument \$depth to be \>\= 1, but it is not\. Found: \(int\) 0',
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

    public function testWithFlagsWorks(): void
    {
        $caster = $this->mockCasterInterface();
        $characterEncoding = $this->mockCharacterEncoding();

        $jsonFormatterA = new JSONFormatter($caster, $characterEncoding);

        $jsonFormatterB = $jsonFormatterA->withFlags($jsonFormatterA->getFlags());
        $jsonFormatterC = $jsonFormatterA->withFlags(1);

        $this->assertNotSame($jsonFormatterA, $jsonFormatterB);
        $this->assertNotSame($jsonFormatterA, $jsonFormatterC);
        $this->assertNotSame($jsonFormatterB, $jsonFormatterC);
        $this->assertSame(0, $jsonFormatterA->getFlags());
        $this->assertSame(0, $jsonFormatterB->getFlags());
        $this->assertSame(1, $jsonFormatterC->getFlags());
    }

    public function testErrorCodeToTextWorks(): void
    {
        $this->assertSame(
            JSONFormatter::getErrorCodeToTextMap()[JSON_ERROR_NONE],
            JSONFormatter::errorCodeToText(JSON_ERROR_NONE)
        );
        $this->assertSame(
            JSONFormatter::getErrorCodeToTextMap()[JSON_ERROR_DEPTH],
            JSONFormatter::errorCodeToText(JSON_ERROR_DEPTH)
        );
        $this->assertSame(
            null,
            JSONFormatter::errorCodeToText(-1)
        );
    }

    public function testGetErrorCodeToTextMapReturnsIntegerIndexedArrayOfStrings(): void
    {
        foreach (JSONFormatter::getErrorCodeToTextMap() as $k => $v) {
            $this->assertIsInt($k);
            $this->assertIsString($v);
        }
    }

    /**
     * @return CasterInterface&MockObject
     */
    private function mockCasterInterface(): CasterInterface
    {
        return $this
            ->getMockBuilder(CasterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return CharacterEncoding&MockObject
     */
    private function mockCharacterEncoding(): CharacterEncoding
    {
        return $this
            ->getMockBuilder(CharacterEncoding::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}