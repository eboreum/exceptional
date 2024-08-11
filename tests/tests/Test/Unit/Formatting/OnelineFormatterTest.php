<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional\Formatting;

use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Formatting\OnelineFormatter;
use Exception;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

use function basename;
use function implode;
use function preg_match;
use function preg_quote;
use function sprintf;

class OnelineFormatterTest extends TestCase
{
    public function testBasics(): void
    {
        $caster = $this->mockCasterInterface();

        $onelineFormatter = new OnelineFormatter($caster);

        $this->assertSame(0, $onelineFormatter->getPreviousThrowableLevel());
        $this->assertSame($caster, $onelineFormatter->getCaster());
    }

    /**
     * @dataProvider providerTestFormatWorks
     */
    public function testFormatWorks(
        string $expectedJSONRegex,
        OnelineFormatter $onelineFormatter,
        Throwable $throwable
    ): void {
        $this->assertMatchesRegularExpression($expectedJSONRegex, $onelineFormatter->format($throwable));
    }

    /**
     * @return array<int, array{0: string, 1: OnelineFormatter, 2: Exception}>
     */
    public function providerTestFormatWorks(): array
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
                (function () {
                    $caster = $this->mockCasterInterface();

                    $caster
                        ->expects($this->exactly(2))
                        ->method('maskString')
                        ->withConsecutive(
                            ['foo'],
                            [
                                $this->callback(static function (string $v) {
                                    return (1 === preg_match('/^#\d+ /', $v));
                                }),
                            ],
                        )
                        ->willReturnOnConsecutiveCalls(
                            'foo',
                            '#0 Lorem'
                        );

                    return new OnelineFormatter($caster);
                })(),
                new Exception('foo'),
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
                (function () {
                    $caster = $this->mockCasterInterface();

                    $caster
                        ->expects($this->exactly(2))
                        ->method('maskString')
                        ->withConsecutive(
                            ['foo'],
                            [
                                $this->callback(static function (string $v) {
                                    return (1 === preg_match('/^#\d+ /', $v));
                                }),
                            ],
                        )
                        ->willReturnOnConsecutiveCalls(
                            'foo',
                            '#0 Lorem'
                        );

                    $onelineFormatter = new OnelineFormatter($caster);

                    /**
                     * @var OnelineFormatter $onelineFormatter
                     */
                    $onelineFormatter = $onelineFormatter->withIsProvidingTimestamp(true);

                    return $onelineFormatter;
                })(),
                new Exception('foo'),
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
                (function () {
                    $caster = $this->mockCasterInterface();

                    $caster
                        ->expects($this->exactly(2))
                        ->method('maskString')
                        ->withConsecutive(
                            ['foo'],
                            [
                                $this->callback(static function (string $v) {
                                    return (1 === preg_match('/^#\d+ /', $v));
                                }),
                            ],
                        )
                        ->willReturnOnConsecutiveCalls(
                            'foo',
                            "#0 Lorem\n#1 Ipsum\n#2 Dolor",
                        );

                    return new OnelineFormatter($caster);
                })(),
                new Exception('foo'),
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
                (function () {
                    $caster = $this->mockCasterInterface();

                    $caster
                        ->expects($this->exactly(6))
                        ->method('maskString')
                        ->withConsecutive(
                            ['foo'],
                            [
                                $this->callback(static function (string $v) {
                                    return (1 === preg_match('/^#\d+ /', $v));
                                }),
                            ],
                            ['bar'],
                            [
                                $this->callback(static function (string $v) {
                                    return (1 === preg_match('/^#\d+ /', $v));
                                }),
                            ],
                            ['baz'],
                            [
                                $this->callback(static function (string $v) {
                                    return (1 === preg_match('/^#\d+ /', $v));
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

                    return new OnelineFormatter($caster);
                })(),
                (static function () {
                    $baz = new LogicException('baz', 2);
                    $bar = new RuntimeException('bar', 1, $baz);

                    return new Exception('foo', 0, $bar);
                })(),
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
                (function () {
                    $caster = $this->mockCasterInterface();

                    $caster
                        ->expects($this->exactly(4))
                        ->method('maskString')
                        ->withConsecutive(
                            ['foo'],
                            [
                                $this->callback(static function (string $v) {
                                    return (1 === preg_match('/^#\d+ /', $v));
                                }),
                            ],
                            ['bar'],
                            [
                                $this->callback(static function (string $v) {
                                    return (1 === preg_match('/^#\d+ /', $v));
                                }),
                            ],
                        )
                        ->willReturnOnConsecutiveCalls(
                            'foo',
                            '#0 Lorem',
                            'bar',
                            '#0 Ipsum',
                        );

                    $onelineFormatter = new OnelineFormatter($caster);

                    /**
                     * @var OnelineFormatter $onelineFormatter
                     */
                    $onelineFormatter = $onelineFormatter->withMaximumPreviousDepth(1);

                    return $onelineFormatter;
                })(),
                (static function () {
                    $bim = new LogicException('bim', 3);
                    $baz = new LogicException('baz', 2, $bim);
                    $bar = new RuntimeException('bar', 1, $baz);

                    return new Exception('foo', 0, $bar);
                })(),
            ],
        ];
    }

    /**
     * @dataProvider providerTestNormalizeStringWorks
     */
    public function testNormalizeStringWorks(string $expected, string $str): void
    {
        $caster = $this->mockCasterInterface();

        $onelineFormatter = new OnelineFormatter($caster);

        $this->assertSame($expected, $onelineFormatter->normalizeString($str));
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    public function providerTestNormalizeStringWorks(): array
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
}
