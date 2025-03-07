<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional\Formatting;

use Closure;
use Eboreum\Caster\CharacterEncoding;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\Formatting\AbstractFormatter;
use Eboreum\Exceptional\Formatting\JSONFormatter;
use Eboreum\PhpunitWithConsecutiveAlternative\MethodCallExpectation;
use Exception;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Test\Unit\Eboreum\Exceptional\AbstractTestCase;
use Throwable;

use function basename;
use function implode;
use function preg_match;
use function preg_quote;
use function sprintf;

use const JSON_ERROR_DEPTH;
use const JSON_ERROR_NONE;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_UNICODE;

#[CoversClass(JSONFormatter::class)]
class JSONFormatterTest extends AbstractTestCase
{
    /**
     * @return array<
     *   array{
     *     string,
     *     Closure(self):array{CasterInterface&MockObject, CharacterEncoding&MockObject},
     *     Throwable,
     *     Closure(JSONFormatter):JSONFormatter,
     *   }
     * >
     */
    public static function providerTestFormatWorks(): array
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
                static function (self $self): array {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $callback = $self->callback(
                        static function (string $v): bool {
                            return 1 === preg_match('/^#0 /', $v);
                        },
                    );

                    $self->expectConsecutiveCalls(
                        $caster,
                        'maskString',
                        new MethodCallExpectation('foo', 'foo'),
                        new MethodCallExpectation('#0 Lorem', $callback),
                    );

                    return [$caster, $characterEncoding];
                },
                new Exception('foo'),
                static function (JSONFormatter $jsonFormatter): JSONFormatter {
                    return $jsonFormatter;
                },
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
                static function (self $self) {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $callback = $self->callback(
                        static function (string $v): bool {
                            return 1 === preg_match('/^#0 /', $v);
                        },
                    );

                    $self->expectConsecutiveCalls(
                        $caster,
                        'maskString',
                        new MethodCallExpectation('foo', 'foo'),
                        new MethodCallExpectation('#0 Lorem', $callback),
                    );

                    return [$caster, $characterEncoding];
                },
                new Exception('foo'),
                static function (JSONFormatter $jsonFormatter): JSONFormatter {
                    return $jsonFormatter->withIsProvidingTimestamp(true);
                },
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
                static function (self $self): array {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $callback = $self->callback(
                        static function (string $v): bool {
                            return 1 === preg_match('/^#0 /', $v);
                        },
                    );

                    $self->expectConsecutiveCalls(
                        $caster,
                        'maskString',
                        new MethodCallExpectation('foo', 'foo'),
                        new MethodCallExpectation('#0 Lorem', $callback),
                        new MethodCallExpectation('bar', 'bar'),
                        new MethodCallExpectation('#0 Ipsum', $callback),
                        new MethodCallExpectation('baz', 'baz'),
                        new MethodCallExpectation('#0 Dolor', $callback),
                    );

                    return [$caster, $characterEncoding];
                },
                (static function () {
                    $baz = new LogicException('baz', 2);
                    $bar = new \RuntimeException('bar', 1, $baz);

                    return new Exception('foo', 0, $bar);
                })(),
                static function (JSONFormatter $jsonFormatter): JSONFormatter {
                    return $jsonFormatter;
                },
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
                ),
                static function (self $self): array {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $callback = $self->callback(
                        static function (string $v): bool {
                            return 1 === preg_match('/^#0 /', $v);
                        },
                    );

                    $self->expectConsecutiveCalls(
                        $caster,
                        'maskString',
                        new MethodCallExpectation('foo', 'foo'),
                        new MethodCallExpectation('#0 Lorem', $callback),
                        new MethodCallExpectation('bar', 'bar'),
                        new MethodCallExpectation('#0 Ipsum', $callback),
                    );

                    return [$caster, $characterEncoding];
                },
                (static function () {
                    $bim = new LogicException('bim', 3);
                    $baz = new LogicException('baz', 2, $bim);
                    $bar = new \RuntimeException('bar', 1, $baz);

                    return new Exception('foo', 0, $bar);
                })(),
                static function (JSONFormatter $jsonFormatter): JSONFormatter {
                    return $jsonFormatter->withMaximumPreviousDepth(1);
                },
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
                ),
                static function (self $self): array {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $callback = $self->callback(
                        static function (string $v): bool {
                            return 1 === preg_match('/^#0 /', $v);
                        },
                    );

                    $self->expectConsecutiveCalls(
                        $caster,
                        'maskString',
                        new MethodCallExpectation('æøå', 'æøå'),
                        new MethodCallExpectation('#0 Lorem', $callback),
                    );

                    return [$caster, $characterEncoding];
                },
                new Exception('æøå'),
                static function (JSONFormatter $jsonFormatter): JSONFormatter {
                    return $jsonFormatter->withFlags(JSON_UNESCAPED_UNICODE);
                },
            ],
        ];
    }

    public function testBasics(): void
    {
        $caster = $this->createMock(CasterInterface::class);
        $characterEncoding = $this->createMock(CharacterEncoding::class);

        $jsonFormatter = new JSONFormatter($caster, $characterEncoding);

        $this->assertSame($characterEncoding, $jsonFormatter->getCharacterEncoding());
        $this->assertSame(0, $jsonFormatter->getPreviousThrowableLevel());
        $this->assertSame($caster, $jsonFormatter->getCaster());
    }

    /**
     * @param Closure(self):array{CasterInterface&MockOBject, CharacterEncoding&MockObject} $factory
     * @param Closure(JSONFormatter):JSONFormatter $jsonFormatterMutator
     */
    #[DataProvider('providerTestFormatWorks')]
    public function testFormatWorks(
        string $expectedJSONRegex,
        Closure $factory,
        Throwable $throwable,
        Closure $jsonFormatterMutator,
    ): void {
        [$caster, $characterEncoding] = $factory($this);

        $jsonFormatter = new JSONFormatter($caster, $characterEncoding);
        $jsonFormatter = $jsonFormatterMutator($jsonFormatter);

        $this->assertMatchesRegularExpression($expectedJSONRegex, $jsonFormatter->format($throwable));
    }

    public function testFormatThrowsExceptionWhenMaximumChildDepthIsReached(): void
    {
        $bar = new \RuntimeException('bar', 1);
        $foo = new Exception('foo', 0, $bar);

        $caster = $this->createMock(CasterInterface::class);
        $characterEncoding = $this->createMock(CharacterEncoding::class);

        $callback = $this->callback(
            static function (string $v): bool {
                return 1 === preg_match('/^#0 /', $v);
            },
        );

        $this->expectConsecutiveCalls(
            $caster,
            'maskString',
            new MethodCallExpectation('foo', 'foo'),
            new MethodCallExpectation('#0 Lorem', $callback),
        );

        $jsonFormatter = new JSONFormatter($caster, $characterEncoding);
        $jsonFormatter = $jsonFormatter->withDepth(1);

        try {
            $jsonFormatter->format($foo);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>format\(',
                            '\$throwable = \(object\) \\\\Exception \{.+\}',
                        '\) inside \(object\) \\\\%s \{',
                            '\$characterEncoding = \(object\) \\\\MockObject_CharacterEncoding_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$caster = \(object\) \\\\MockObject_CasterInterface_[0-9a-f]{8}',
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
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
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
        $foo = new Exception('foo', 0, $bar);

        $caster = $this->createMock(CasterInterface::class);
        $characterEncoding = $this->createMock(CharacterEncoding::class);

        $callback = $this->callback(
            static function (string $v): bool {
                return 1 === preg_match('/^#0 /', $v);
            },
        );

        $this->expectConsecutiveCalls(
            $caster,
            'maskString',
            new MethodCallExpectation('foo', 'foo'),
            new MethodCallExpectation('#0 Lorem', $callback),
        );

        $jsonFormatter = new JSONFormatter($caster, $characterEncoding);
        $jsonFormatter = $jsonFormatter->withDepth(1);

        try {
            $jsonFormatter->format($foo);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>format\(',
                            '\$throwable = \(object\) \\\\Exception \{.+\}',
                        '\) inside \(object\) \\\\%s \{',
                            '\$characterEncoding = \(object\) \\\\MockObject_CharacterEncoding_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$caster = \(object\) \\\\MockObject_CasterInterface_[0-9a-f]{8}',
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
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
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

    public function testFormatThrowsExceptionWhenJsonEncodeFailsByReturningFalse(): void
    {
        $caster = $this->createMock(CasterInterface::class);
        $characterEncoding = $this->createMock(CharacterEncoding::class);

        $jsonFormatter = new class ($caster, $characterEncoding) extends JSONFormatter
        {
            /**
             * @override
             */
            protected function formatInner(Throwable $throwable, JSONFormatter $topLevelJSONFormatter): stdClass
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

        $exception = new Exception();

        try {
            $jsonFormatter->format($exception);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>format\(',
                            '\$throwable = \(object\) \\\\Exception \{.+\}',
                        '\) inside \(object\) \\\\%s@anonymous\/in\/.+\/%s:\d+ \{',
                            '\\\\%s\-\>\$characterEncoding = \(object\) \\\\MockObject_CharacterEncoding_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$caster = \(object\) \\\\MockObject_CasterInterface_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$previousThrowableLevel = \(int\) 0',
                            ', \\\\%s\-\>\$maximumPreviousDepth = \(null\) null',
                            ', \\\\%s\-\>\$isProvidingTimestamp = \(bool\) false',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(JSONFormatter::class, '/'),
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
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
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

    public function testFormatThrowsExceptionWhenJsonEncodeFailsByThrowingException(): void
    {
        $caster = $this->createMock(CasterInterface::class);
        $characterEncoding = $this->createMock(CharacterEncoding::class);

        $jsonFormatter = new class ($caster, $characterEncoding) extends JSONFormatter
        {
            /**
             * @override
             */
            protected function formatInner(Throwable $throwable, JSONFormatter $topLevelJSONFormatter): stdClass
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

        $exception = new Exception();

        try {
            $jsonFormatter->format($exception);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>format\(',
                            '\$throwable = \(object\) \\\\Exception \{.+\}',
                        '\) inside \(object\) \\\\%s@anonymous\/in\/.+\/%s:\d+ \{',
                            '\\\\%s\-\>\$characterEncoding = \(object\) \\\\MockObject_CharacterEncoding_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$caster = \(object\) \\\\MockObject_CasterInterface_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$previousThrowableLevel = \(int\) 0',
                            ', \\\\%s\-\>\$maximumPreviousDepth = \(null\) null',
                            ', \\\\%s\-\>\$isProvidingTimestamp = \(bool\) false',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(JSONFormatter::class, '/'),
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
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
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
            $this->assertIsObject($currentException);
            $this->assertSame('JsonException', $currentException::class);
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
        $caster = $this->createMock(CasterInterface::class);
        $characterEncoding = $this->createMock(CharacterEncoding::class);

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

    public function testWithFlagsWorks(): void
    {
        $caster = $this->createMock(CasterInterface::class);
        $characterEncoding = $this->createMock(CharacterEncoding::class);

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
            $this->assertIsInt($k); // @phpstan-ignore-line
            $this->assertIsString($v); // @phpstan-ignore-line
        }
    }
}
