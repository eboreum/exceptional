<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional\Formatting;

use Closure;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Formatting\DefaultFormatter;
use Exception;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

use function basename;
use function implode;
use function in_array;
use function preg_match;
use function preg_quote;
use function sprintf;

#[CoversClass(DefaultFormatter::class)]
class DefaultFormatterTest extends TestCase
{
    /**
     * @return array<
     *   int,
     *   array{
     *     string,
     *     Closure(self):(CasterInterface&MockObject),
     *     Throwable,
     *     Closure(DefaultFormatter):DefaultFormatter,
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
                        '\nMessage:',
                        '\n    foo',
                        '\nFile: .+\/[^\/]+\/%s',
                        '\nLine: \d+',
                        '\nCode: 0',
                        '\nStacktrace:',
                        '(\n    #0 Lorem)+',
                        '\nPrevious: \(None\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function (self $self): CasterInterface&MockObject {
                    $caster = $self->createMock(CasterInterface::class);

                    $caster
                        ->expects($self->any())
                        ->method('maskString')
                        ->with(
                            $self->callback(
                                static function (string $v) {
                                    return (
                                        'foo' === $v
                                        || (1 === preg_match('/^#\d+ /', $v))
                                    );
                                },
                            ),
                        )
                        ->willReturnCallback(
                            static function (string $v): string {
                                if ('foo' === $v) {
                                    return 'foo';
                                }

                                if (1 === preg_match('/^#\d+ /', $v)) {
                                    return '#0 Lorem';
                                }

                                throw new Exception(sprintf(
                                    'Uncovered case for $v = %s',
                                    Caster::getInstance()->castTyped($v),
                                ));
                            },
                        );

                    return $caster;
                },
                new Exception('foo'),
                static function (DefaultFormatter $defaultFormatter): DefaultFormatter {
                    return $defaultFormatter;
                },
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\\\\Exception \(\d{4}\-\d{2}\-\d{2}T\d{2}\:\d{2}\:\d{2}[\+\-]\d{2}\:\d{2}\)',
                        '\nMessage:',
                        '\n    foo',
                        '\nFile: .+\/[^\/]+\/%s',
                        '\nLine: \d+',
                        '\nCode: 0',
                        '\nStacktrace:',
                        '(\n    #0 Lorem)+',
                        '\nPrevious: \(None\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function (self $self): CasterInterface&MockObject {
                    $caster = $self->createMock(CasterInterface::class);

                    $caster
                        ->expects($self->any())
                        ->method('maskString')
                        ->with(
                            $self->callback(
                                static function (string $v) {
                                    return (
                                        'foo' === $v
                                        || (1 === preg_match('/^#\d+ /', $v))
                                    );
                                },
                            ),
                        )
                        ->willReturnCallback(
                            static function (string $v): string {
                                if ('foo' === $v) {
                                    return 'foo';
                                }

                                if (1 === preg_match('/^#\d+ /', $v)) {
                                    return '#0 Lorem';
                                }

                                throw new Exception(sprintf(
                                    'Uncovered case for $v = %s',
                                    Caster::getInstance()->castTyped($v),
                                ));
                            },
                        );

                    return $caster;
                },
                new Exception('foo'),
                static function (DefaultFormatter $defaultFormatter): DefaultFormatter {
                    return $defaultFormatter->withIsProvidingTimestamp(true);
                },
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\\\\Exception',
                        '\nMessage:',
                        '\n    foo',
                        '\nFile: .+\/[^\/]+\/%s',
                        '\nLine: \d+',
                        '\nCode: 0',
                        '\nStacktrace:',
                        '(\n    #0 Lorem)+',
                        '\nPrevious: \(2 more\)',
                        '\n    \\\\RuntimeException',
                        '\n    Message:',
                        '\n        bar',
                        '\n    File: .+\/[^\/]+\/%s',
                        '\n    Line: \d+',
                        '\n    Code: 1',
                        '\n    Stacktrace:',
                        '(\n        #0 Lorem)+',
                        '\n    Previous: \(1 more\)',
                        '\n        \\\\LogicException',
                        '\n        Message:',
                        '\n            baz',
                        '\n        File: .+\/[^\/]+\/%s',
                        '\n        Line: \d+',
                        '\n        Code: 2',
                        '\n        Stacktrace:',
                        '(\n            #0 Lorem)+',
                        '\n        Previous: \(None\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function (self $self): CasterInterface&MockObject {
                    $caster = $self->createMock(CasterInterface::class);

                    $caster
                        ->expects($self->any())
                        ->method('maskString')
                        ->with(
                            $self->callback(
                                static function (string $v) {
                                    return (
                                        in_array($v, ['foo', 'bar', 'baz'], true)
                                        || (1 === preg_match('/^#\d+ /', $v))
                                    );
                                },
                            ),
                        )
                        ->willReturnCallback(
                            static function (string $v) {
                                if (in_array($v, ['foo', 'bar', 'baz'], true)) {
                                    return $v;
                                }

                                if (1 === preg_match('/^#\d+ /', $v)) {
                                    return '#0 Lorem';
                                }

                                throw new Exception(sprintf(
                                    'Uncovered case for $v = %s',
                                    Caster::getInstance()->castTyped($v),
                                ));
                            },
                        );

                    return $caster;
                },
                (static function () {
                    $baz = new LogicException('baz', 2);
                    $bar = new RuntimeException('bar', 1, $baz);

                    return new Exception('foo', 0, $bar);
                })(),
                static function (DefaultFormatter $defaultFormatter): DefaultFormatter {
                    return $defaultFormatter;
                },
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\\\\Exception',
                        '\nMessage:',
                        '\n    foo',
                        '\nFile: .+\/[^\/]+\/%s',
                        '\nLine: \d+',
                        '\nCode: 0',
                        '\nStacktrace:',
                        '(\n    #0 Lorem)+',
                        '\nPrevious: \(3 more\)',
                        '\n    \\\\RuntimeException',
                        '\n    Message:',
                        '\n        bar',
                        '\n    File: .+\/[^\/]+\/%s',
                        '\n    Line: \d+',
                        '\n    Code: 1',
                        '\n    Stacktrace:',
                        '(\n        #0 Lorem)+',
                        '\n    Previous: \(2 more\) \(omitted\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function (self $self): CasterInterface&MockObject {
                    $caster = $self->createMock(CasterInterface::class);

                    $caster
                        ->expects($self->any())
                        ->method('maskString')
                        ->with(
                            $self->callback(
                                static function (string $v) {
                                    return (
                                        in_array($v, ['foo', 'bar', 'baz', 'bim'], true)
                                        || (1 === preg_match('/^#\d+ /', $v))
                                    );
                                },
                            ),
                        )
                        ->willReturnCallback(
                            static function (string $v) {
                                if (in_array($v, ['foo', 'bar', 'baz', 'bim'], true)) {
                                    return $v;
                                }

                                if (1 === preg_match('/^#\d+ /', $v)) {
                                    return '#0 Lorem';
                                }

                                throw new Exception(sprintf(
                                    'Uncovered case for $v = %s',
                                    Caster::getInstance()->castTyped($v),
                                ));
                            },
                        );

                    return $caster;
                },
                (static function () {
                    $bim = new LogicException('bim', 3);
                    $baz = new LogicException('baz', 2, $bim);
                    $bar = new RuntimeException('bar', 1, $baz);

                    return new Exception('foo', 0, $bar);
                })(),
                static function (DefaultFormatter $defaultFormatter): DefaultFormatter {
                    return $defaultFormatter->withMaximumPreviousDepth(1);
                },
            ],
        ];
    }

    public function testBasics(): void
    {
        $caster = $this->createMock(CasterInterface::class);

        $defaultFormatter = new DefaultFormatter($caster);

        $this->assertSame('    ', $defaultFormatter->getIndentationCharacters());
        $this->assertSame($caster, $defaultFormatter->getCaster());
    }

    /**
     * @param Closure(self):(CasterInterface&MockObject) $casterFactory
     * @param Closure(DefaultFormatter):DefaultFormatter $defaultFormatterMutator
     */
    #[DataProvider('providerTestFormatWorks')]
    public function testFormatWorks(
        string $expectedJSONRegex,
        Closure $casterFactory,
        Throwable $throwable,
        Closure $defaultFormatterMutator,
    ): void {
        $caster = $casterFactory($this);

        $defaultFormatter = new DefaultFormatter($caster);
        $defaultFormatter = $defaultFormatterMutator($defaultFormatter);

        $this->assertMatchesRegularExpression($expectedJSONRegex, $defaultFormatter->format($throwable));
    }

    public function testWithIndentationCharactersWorks(): void
    {
        $caster = $this->createMock(CasterInterface::class);

        $defaultFormatterA = new DefaultFormatter($caster);
        $defaultFormatterB = $defaultFormatterA->withIndentationCharacters('    ');
        $defaultFormatterC = $defaultFormatterA->withIndentationCharacters('+?+');

        $this->assertNotSame($defaultFormatterA, $defaultFormatterB);
        $this->assertNotSame($defaultFormatterA, $defaultFormatterC);
        $this->assertNotSame($defaultFormatterB, $defaultFormatterC);
        $this->assertSame('    ', $defaultFormatterA->getIndentationCharacters());
        $this->assertSame('    ', $defaultFormatterB->getIndentationCharacters());
        $this->assertSame('+?+', $defaultFormatterC->getIndentationCharacters());
    }
}
