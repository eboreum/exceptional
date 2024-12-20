<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional\Formatting;

use Closure;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Formatting\OnelineFormatter;
use Eboreum\PhpunitWithConsecutiveAlternative\MethodCallExpectation;
use Exception;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Test\Unit\Eboreum\Exceptional\AbstractTestCase;
use Throwable;

use function basename;
use function implode;
use function preg_match;
use function preg_quote;
use function sprintf;

#[CoversClass(OnelineFormatter::class)]
class OnelineFormatterTest extends AbstractTestCase
{
    /**
     * @return array<
     *   int,
     *   array{
     *     string,
     *     Closure(self):(CasterInterface&MockObject),
     *     Throwable,
     *     Closure(OnelineFormatter):OnelineFormatter,
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
                        '\\\\Exception',
                        '\. Message: foo',
                        '\. File: .+\/[^\/]+\/%s',
                        '\. Line: \d+',
                        '\. Code: 0',
                        '\. Stacktrace: #0 Lorem',
                        '\. Previous: \(None\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function (self $self): CasterInterface&MockObject {
                    $caster = $self->createMock(CasterInterface::class);

                    $callback = $self->callback(
                        static function (string $v): bool {
                            return 1 === preg_match('/^#\d+ /', $v);
                        },
                    );

                    $self->expectConsecutiveCalls(
                        $caster,
                        'maskString',
                        new MethodCallExpectation('foo', 'foo'),
                        new MethodCallExpectation('#0 Lorem', $callback),
                    );

                    return $caster;
                },
                new Exception('foo'),
                static function (OnelineFormatter $onelineFormatter): OnelineFormatter {
                    return $onelineFormatter;
                },
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\\\\Exception \(\d{4}\-\d{2}\-\d{2}T\d{2}\:\d{2}\:\d{2}[\+\-]\d{2}\:\d{2}\)',
                        '\. Message: foo',
                        '\. File: .+\/[^\/]+\/%s',
                        '\. Line: \d+',
                        '\. Code: 0',
                        '\. Stacktrace: #0 Lorem',
                        '\. Previous: \(None\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function (self $self): CasterInterface&MockObject {
                    $caster = $self->createMock(CasterInterface::class);

                    $callback = $self->callback(
                        static function (string $v): bool {
                            return 1 === preg_match('/^#\d+ /', $v);
                        },
                    );

                    $self->expectConsecutiveCalls(
                        $caster,
                        'maskString',
                        new MethodCallExpectation('foo', 'foo'),
                        new MethodCallExpectation('#0 Lorem', $callback),
                    );

                    return $caster;
                },
                new Exception('foo'),
                static function (OnelineFormatter $onelineFormatter): OnelineFormatter {
                    return $onelineFormatter->withIsProvidingTimestamp(true);
                },
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\\\\Exception',
                        '\. Message: foo',
                        '\. File: .+\/[^\/]+\/%s',
                        '\. Line: \d+',
                        '\. Code: 0',
                        '\. Stacktrace: #0 Lorem #1 Ipsum #2 Dolor',
                        '\. Previous: \(None\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function (self $self): CasterInterface&MockObject {
                    $caster = $self->createMock(CasterInterface::class);

                    $callback = $self->callback(
                        static function (string $v): bool {
                            return 1 === preg_match('/^#\d+ /', $v);
                        },
                    );

                    $self->expectConsecutiveCalls(
                        $caster,
                        'maskString',
                        new MethodCallExpectation('foo', 'foo'),
                        new MethodCallExpectation("#0 Lorem\n#1 Ipsum\n#2 Dolor", $callback),
                    );

                    return $caster;
                },
                new Exception('foo'),
                static function (OnelineFormatter $onelineFormatter): OnelineFormatter {
                    return $onelineFormatter;
                },
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\\\\Exception',
                        '\. Message: foo',
                        '\. File: .+\/[^\/]+\/%s',
                        '\. Line: \d+',
                        '\. Code: 0',
                        '\. Stacktrace: #0 Lorem',
                        '\. Previous: \(2 more\)',
                            ' \\\\RuntimeException',
                            '\. Message: bar',
                            '\. File: .+\/[^\/]+\/%s',
                            '\. Line: \d+',
                            '\. Code: 1',
                            '\. Stacktrace: #0 Ipsum',
                            '\. Previous: \(1 more\)',
                                ' \\\\LogicException',
                                '\. Message: baz',
                                '\. File: .+\/[^\/]+\/%s',
                                '\. Line: \d+',
                                '\. Code: 2',
                                '\. Stacktrace: #0 Dolor',
                                '\. Previous: \(None\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function (self $self): CasterInterface&MockObject {
                    $caster = $self->createMock(CasterInterface::class);

                    $callback = $self->callback(
                        static function (string $v): bool {
                            return 1 === preg_match('/^#\d+ /', $v);
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

                    return $caster;
                },
                (static function (): Exception {
                    $baz = new LogicException('baz', 2);
                    $bar = new RuntimeException('bar', 1, $baz);

                    return new Exception('foo', 0, $bar);
                })(),
                static function (OnelineFormatter $onelineFormatter): OnelineFormatter {
                    return $onelineFormatter;
                },
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\\\\Exception',
                        '\. Message: foo',
                        '\. File: .+\/[^\/]+\/%s',
                        '\. Line: \d+',
                        '\. Code: 0',
                        '\. Stacktrace: #0 Lorem',
                        '\. Previous: \(3 more\)',
                            ' \\\\RuntimeException',
                            '\. Message: bar',
                            '\. File: .+\/[^\/]+\/%s',
                            '\. Line: \d+',
                            '\. Code: 1',
                            '\. Stacktrace: #0 Ipsum',
                            '\. Previous: \(2 more\) \(omitted\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function (self $self): CasterInterface&MockObject {
                    $caster = $self->createMock(CasterInterface::class);

                    $callback = $self->callback(
                        static function (string $v): bool {
                            return 1 === preg_match('/^#\d+ /', $v);
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

                    return $caster;
                },
                (static function (): Exception {
                    $bim = new LogicException('bim', 3);
                    $baz = new LogicException('baz', 2, $bim);
                    $bar = new RuntimeException('bar', 1, $baz);

                    return new Exception('foo', 0, $bar);
                })(),
                static function (OnelineFormatter $onelineFormatter): OnelineFormatter {
                    return $onelineFormatter->withMaximumPreviousDepth(1);
                },
            ],
        ];
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    public static function providerTestNormalizeStringWorks(): array
    {
        return [
            [
                '',
                '',
            ],
            [
                'foo',
                'foo',
            ],
            [
                'foo bar',
                "foo\nbar",
            ],
            [
                'foo     bar',
                "foo\n\n\n\n\nbar",
            ],
        ];
    }

    public function testBasics(): void
    {
        $caster = $this->createMock(CasterInterface::class);

        $onelineFormatter = new OnelineFormatter($caster);

        $this->assertSame(0, $onelineFormatter->getPreviousThrowableLevel());
        $this->assertSame($caster, $onelineFormatter->getCaster());
    }

    /**
     * @param Closure(self):(CasterInterface&MockObject) $casterFactory
     * @param Closure(OnelineFormatter):OnelineFormatter $onelineFormatterMutator
     */
    #[DataProvider('providerTestFormatWorks')]
    public function testFormatWorks(
        string $expectedJSONRegex,
        Closure $casterFactory,
        Throwable $throwable,
        Closure $onelineFormatterMutator,
    ): void {
        $caster = $casterFactory($this);

        $onelineFormatter = new OnelineFormatter($caster);
        $onelineFormatter = $onelineFormatterMutator($onelineFormatter);

        $this->assertMatchesRegularExpression($expectedJSONRegex, $onelineFormatter->format($throwable));
    }

    #[DataProvider('providerTestNormalizeStringWorks')]
    public function testNormalizeStringWorks(string $expected, string $str): void
    {
        $caster = $this->createMock(CasterInterface::class);

        $onelineFormatter = new OnelineFormatter($caster);

        $this->assertSame($expected, $onelineFormatter->normalizeString($str));
    }
}
