<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional\Formatting;

use Closure;
use Eboreum\Caster\CharacterEncoding;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Formatting\HTML5TableFormatter;
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

#[CoversClass(HTML5TableFormatter::class)]
class HTML5TableFormatterTest extends AbstractTestCase
{
    /**
     * @return array<
     *   int, array{
     *     string,
     *     Closure(self):array{CasterInterface&MockObject, CharacterEncoding&MockObject},
     *     Throwable,
     *     Closure(HTML5TableFormatter):HTML5TableFormatter,
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
                        '\<table\>',
                            '\<tbody\>',
                                '\<tr\>',
                                    '\<td colspan="2"\>',
                                        '\<h1\>\\\\Exception\<\/h1\>',
                                    '<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Message\:<\/td\>',
                                    '\<td\>foo<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>File\:<\/td\>',
                                    '\<td\>.+\/[^\/]+\/%s<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Line\:<\/td\>',
                                    '\<td\>\d+<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Code\:<\/td\>',
                                    '\<td\>0<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Stacktrace\:<\/td\>',
                                    '\<td\>',
                                        '\<pre\>#0 Lorem<\/pre\>',
                                    '<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Previous\:<\/td\>',
                                    '\<td\>\(None\)<\/td\>',
                                '<\/tr\>',
                            '<\/tbody\>',
                        '<\/table\>',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function (self $self): array {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $self->expectConsecutiveCalls(
                        $caster,
                        'maskString',
                        new MethodCallExpectation('foo', 'foo'),
                        new MethodCallExpectation(
                            '#0 Lorem',
                            $self->callback(
                                static function (string $v) {
                                    return 1 === preg_match('/^#\d+ /', $v);
                                },
                            ),
                        ),
                    );

                    return [$caster, $characterEncoding];
                },
                new Exception('foo'),
                static function (HTML5TableFormatter $html5TableFormatter): HTML5TableFormatter {
                    return $html5TableFormatter;
                },
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\<table\>',
                        '\n  \<tbody\>',
                        '\n    \<tr\>',
                        '\n      \<td colspan="2"\>',
                        '\n        \<h1\>\\\\Exception\<\/h1\>',
                        '\n      \<\/td\>',
                        '\n    <\/tr\>',
                        '\n    \<tr\>',
                        '\n      \<td\>Message\:<\/td\>',
                        '\n      \<td\>foo<\/td\>',
                        '\n    <\/tr\>',
                        '\n    \<tr\>',
                        '\n      \<td\>File\:<\/td\>',
                        '\n      \<td\>.+\/[^\/]+\/%s<\/td\>',
                        '\n    <\/tr\>',
                        '\n    \<tr\>',
                        '\n      \<td\>Line\:<\/td\>',
                        '\n      \<td\>\d+<\/td\>',
                        '\n    <\/tr\>',
                        '\n    \<tr\>',
                        '\n      \<td\>Code\:<\/td\>',
                        '\n      \<td\>0<\/td\>',
                        '\n    <\/tr\>',
                        '\n    \<tr\>',
                        '\n      \<td\>Stacktrace\:<\/td\>',
                        '\n      \<td\>',
                        '\n        \<pre\>#0 Lorem<\/pre\>',
                        '\n      <\/td\>',
                        '\n    <\/tr\>',
                        '\n    \<tr\>',
                        '\n      \<td\>Previous\:<\/td\>',
                        '\n      \<td\>\(None\)<\/td\>',
                        '\n    <\/tr\>',
                        '\n  <\/tbody\>',
                        '\n<\/table\>',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function (self $self): array {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $self->expectConsecutiveCalls(
                        $caster,
                        'maskString',
                        new MethodCallExpectation('foo', 'foo'),
                        new MethodCallExpectation(
                            '#0 Lorem',
                            $self->callback(
                                static function (string $v) {
                                    return 1 === preg_match('/^#\d+ /', $v);
                                },
                            ),
                        ),
                    );

                    return [$caster, $characterEncoding];
                },
                new Exception('foo'),
                static function (HTML5TableFormatter $html5TableFormatter): HTML5TableFormatter {
                    return $html5TableFormatter->withIsPrettyPrinting(true);
                },
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\<table\>',
                            '\<tbody\>',
                                '\<tr\>',
                                    '\<td colspan="2"\>',
                                        '\<h1\>\\\\Exception\<\/h1\>',
                                    '<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Message\:<\/td\>',
                                    '\<td\>&lt;p class=&quot;mellon&quot;&gt;foo&lt;\/p&gt;<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>File\:<\/td\>',
                                    '\<td\>.+\/[^\/]+\/%s<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Line\:<\/td\>',
                                    '\<td\>\d+<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Code\:<\/td\>',
                                    '\<td\>0<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Stacktrace\:<\/td\>',
                                    '\<td\>',
                                        '\<pre\>#0 Lorem<\/pre\>',
                                    '<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Previous\:<\/td\>',
                                    '\<td\>\(None\)<\/td\>',
                                '<\/tr\>',
                            '<\/tbody\>',
                        '<\/table\>',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function (self $self) {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $self->expectConsecutiveCalls(
                        $caster,
                        'maskString',
                        new MethodCallExpectation('<p class="mellon">foo</p>', '<p class="mellon">foo</p>'),
                        new MethodCallExpectation(
                            '#0 Lorem',
                            $self->callback(
                                static function (string $v) {
                                    return 1 === preg_match('/^#\d+ /', $v);
                                },
                            ),
                        ),
                    );

                    return [$caster, $characterEncoding];
                },
                new Exception('<p class="mellon">foo</p>'),
                static function (HTML5TableFormatter $html5TableFormatter): HTML5TableFormatter {
                    return $html5TableFormatter;
                },
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\<table\>',
                            '\<tbody\>',
                                '\<tr\>',
                                    '\<td colspan="2"\>',
                                        '\<h1\>\\\\Exception\<\/h1\>',
                                    '<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Time\:<\/td\>',
                                    '\<td\>\d{4}\-\d{2}\-\d{2}T\d{2}\:\d{2}\:\d{2}[\+\-]\d{2}\:\d{2}<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Message\:<\/td\>',
                                    '\<td\>foo<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>File\:<\/td\>',
                                    '\<td\>.+\/[^\/]+\/%s<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Line\:<\/td\>',
                                    '\<td\>\d+<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Code\:<\/td\>',
                                    '\<td\>0<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Stacktrace\:<\/td\>',
                                    '\<td\>',
                                        '\<pre\>#0 Lorem<\/pre\>',
                                    '<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Previous\:<\/td\>',
                                    '\<td\>\(None\)<\/td\>',
                                '<\/tr\>',
                            '<\/tbody\>',
                        '<\/table\>',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function (self $self): array {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $self->expectConsecutiveCalls(
                        $caster,
                        'maskString',
                        new MethodCallExpectation('foo', 'foo'),
                        new MethodCallExpectation(
                            '#0 Lorem',
                            $self->callback(
                                static function (string $v) {
                                    return 1 === preg_match('/^#\d+ /', $v);
                                },
                            ),
                        ),
                    );

                    return [$caster, $characterEncoding];
                },
                new Exception('foo'),
                static function (HTML5TableFormatter $html5TableFormatter): HTML5TableFormatter {
                    return $html5TableFormatter->withIsProvidingTimestamp(true);
                },
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\<table\>',
                            '\<tbody\>',
                                '\<tr\>',
                                    '\<td colspan="2"\>',
                                        '\<h1\>\\\\Exception\<\/h1\>',
                                    '<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Message\:<\/td\>',
                                    '\<td\>foo<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>File\:<\/td\>',
                                    '\<td\>.+\/[^\/]+\/%s<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Line\:<\/td\>',
                                    '\<td\>\d+<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Code\:<\/td\>',
                                    '\<td\>0<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Stacktrace\:<\/td\>',
                                    '\<td\>',
                                        '\<pre\>#0 Lorem<\/pre\>',
                                    '<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Previous\:<\/td\>',
                                     '\<td\>\(2 more\)',
                                        '\<table\>',
                                            '\<tbody\>',
                                                '\<tr\>',
                                                    '\<td colspan="2"\>',
                                                        '\<h1\>\\\\RuntimeException\<\/h1\>',
                                                    '<\/td\>',
                                                '<\/tr\>',
                                                '\<tr\>',
                                                    '\<td\>Message\:<\/td\>',
                                                    '\<td\>bar<\/td\>',
                                                '<\/tr\>',
                                                '\<tr\>',
                                                    '\<td\>File\:<\/td\>',
                                                    '\<td\>.+\/[^\/]+\/%s<\/td\>',
                                                '<\/tr\>',
                                                '\<tr\>',
                                                    '\<td\>Line\:<\/td\>',
                                                    '\<td\>\d+<\/td\>',
                                                '<\/tr\>',
                                                '\<tr\>',
                                                    '\<td\>Code\:<\/td\>',
                                                    '\<td\>1<\/td\>',
                                                '<\/tr\>',
                                                '\<tr\>',
                                                    '\<td\>Stacktrace\:<\/td\>',
                                                    '\<td\>',
                                                        '\<pre\>#0 Lorem<\/pre\>',
                                                    '<\/td\>',
                                                '<\/tr\>',
                                                '\<tr\>',
                                                    '\<td\>Previous\:<\/td\>',
                                                    '\<td\>\(1 more\)',
                                                        '\<table\>',
                                                            '\<tbody\>',
                                                                '\<tr\>',
                                                                    '\<td colspan="2"\>',
                                                                        '\<h1\>\\\\LogicException\<\/h1\>',
                                                                    '<\/td\>',
                                                                '<\/tr\>',
                                                                '\<tr\>',
                                                                    '\<td\>Message\:<\/td\>',
                                                                    '\<td\>baz<\/td\>',
                                                                '<\/tr\>',
                                                                '\<tr\>',
                                                                    '\<td\>File\:<\/td\>',
                                                                    '\<td\>.+\/[^\/]+\/%s<\/td\>',
                                                                '<\/tr\>',
                                                                '\<tr\>',
                                                                    '\<td\>Line\:<\/td\>',
                                                                    '\<td\>\d+<\/td\>',
                                                                '<\/tr\>',
                                                                '\<tr\>',
                                                                    '\<td\>Code\:<\/td\>',
                                                                    '\<td\>2<\/td\>',
                                                                '<\/tr\>',
                                                                '\<tr\>',
                                                                    '\<td\>Stacktrace\:<\/td\>',
                                                                    '\<td\>',
                                                                        '\<pre\>#0 Lorem<\/pre\>',
                                                                    '<\/td\>',
                                                                '<\/tr\>',
                                                                '\<tr\>',
                                                                    '\<td\>Previous\:<\/td\>',
                                                                    '\<td\>\(None\)<\/td\>',
                                                                '<\/tr\>',
                                                            '<\/tbody\>',
                                                        '<\/table\>',
                                                    '<\/td\>',
                                                '<\/tr\>',
                                            '<\/tbody\>',
                                        '<\/table\>',
                                    '<\/td\>',
                                '<\/tr\>',
                            '<\/tbody\>',
                        '<\/table\>',
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
                        static function (string $v) {
                            return 1 === preg_match('/^#\d+ /', $v);
                        },
                    );

                    $self->expectConsecutiveCalls(
                        $caster,
                        'maskString',
                        new MethodCallExpectation('foo', 'foo'),
                        new MethodCallExpectation('#0 Lorem', $callback),
                        new MethodCallExpectation('bar', 'bar'),
                        new MethodCallExpectation('#0 Lorem', $callback),
                        new MethodCallExpectation('baz', 'baz'),
                        new MethodCallExpectation('#0 Lorem', $callback),
                    );

                    return [$caster, $characterEncoding];
                },
                (static function () {
                    $baz = new LogicException('baz', 2);
                    $bar = new RuntimeException('bar', 1, $baz);

                    return new Exception('foo', 0, $bar);
                })(),
                static function (HTML5TableFormatter $html5TableFormatter): HTML5TableFormatter {
                    return $html5TableFormatter;
                },
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\<table\>',
                            '\<tbody\>',
                                '\<tr\>',
                                    '\<td colspan="2"\>',
                                        '\<h1\>\\\\Exception\<\/h1\>',
                                    '<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Message\:<\/td\>',
                                    '\<td\>foo<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>File\:<\/td\>',
                                    '\<td\>.+\/[^\/]+\/%s<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Line\:<\/td\>',
                                    '\<td\>\d+<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Code\:<\/td\>',
                                    '\<td\>0<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Stacktrace\:<\/td\>',
                                    '\<td\>',
                                        '\<pre\>#0 Lorem<\/pre\>',
                                    '<\/td\>',
                                '<\/tr\>',
                                '\<tr\>',
                                    '\<td\>Previous\:<\/td\>',
                                     '\<td\>\(3 more\)',
                                        '\<table\>',
                                            '\<tbody\>',
                                                '\<tr\>',
                                                    '\<td colspan="2"\>',
                                                        '\<h1\>\\\\RuntimeException\<\/h1\>',
                                                    '<\/td\>',
                                                '<\/tr\>',
                                                '\<tr\>',
                                                    '\<td\>Message\:<\/td\>',
                                                    '\<td\>bar<\/td\>',
                                                '<\/tr\>',
                                                '\<tr\>',
                                                    '\<td\>File\:<\/td\>',
                                                    '\<td\>.+\/[^\/]+\/%s<\/td\>',
                                                '<\/tr\>',
                                                '\<tr\>',
                                                    '\<td\>Line\:<\/td\>',
                                                    '\<td\>\d+<\/td\>',
                                                '<\/tr\>',
                                                '\<tr\>',
                                                    '\<td\>Code\:<\/td\>',
                                                    '\<td\>1<\/td\>',
                                                '<\/tr\>',
                                                '\<tr\>',
                                                    '\<td\>Stacktrace\:<\/td\>',
                                                    '\<td\>',
                                                        '\<pre\>#0 Lorem<\/pre\>',
                                                    '<\/td\>',
                                                '<\/tr\>',
                                                '\<tr\>',
                                                    '\<td\>Previous\:<\/td\>',
                                                    '\<td\>\(2 more\) \(omitted\)\<\/td\>',
                                                '<\/tr\>',
                                            '<\/tbody\>',
                                        '<\/table\>',
                                    '<\/td\>',
                                '<\/tr\>',
                            '<\/tbody\>',
                        '<\/table\>',
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
                        static function (string $v) {
                            return 1 === preg_match('/^#\d+ /', $v);
                        },
                    );

                    $self->expectConsecutiveCalls(
                        $caster,
                        'maskString',
                        new MethodCallExpectation('foo', 'foo'),
                        new MethodCallExpectation('#0 Lorem', $callback),
                        new MethodCallExpectation('bar', 'bar'),
                        new MethodCallExpectation('#0 Lorem', $callback),
                    );

                    return [$caster, $characterEncoding];
                },
                (static function () {
                    $bim = new LogicException('bim', 3);
                    $baz = new LogicException('baz', 2, $bim);
                    $bar = new RuntimeException('bar', 1, $baz);

                    return new Exception('foo', 0, $bar);
                })(),
                static function (HTML5TableFormatter $html5TableFormatter): HTML5TableFormatter {
                    return $html5TableFormatter->withMaximumPreviousDepth(1);
                },
            ],
        ];
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    public static function providerTestHtmlEncodeWorks(): array
    {
        return [
            [
                'foo',
                'foo',
            ],
            [
                '&lt;p class=&quot;mellon&quot;&gt;æøå&lt;/p&gt;',
                '<p class="mellon">æøå</p>',
            ],
            [
                "foo\nbar",
                "foo\nbar",
            ],
        ];
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    public static function providerTestHtmlEncodeWithLn2BrWorks(): array
    {
        return [
            [
                'foo',
                'foo',
            ],
            [
                '&lt;p class=&quot;mellon&quot;&gt;æøå&lt;/p&gt;',
                '<p class="mellon">æøå</p>',
            ],
            [
                'foo<br>bar',
                "foo\nbar",
            ],
            [
                'foo<br><br><br>bar',
                "foo\n\n\nbar",
            ],
        ];
    }

    public function testBasics(): void
    {
        $caster = $this->createMock(CasterInterface::class);
        $characterEncoding = $this->createMock(CharacterEncoding::class);

        $html5TableFormatter = new HTML5TableFormatter($caster, $characterEncoding);

        $this->assertSame($caster, $html5TableFormatter->getCaster());
        $this->assertSame($characterEncoding, $html5TableFormatter->getCharacterEncoding());
    }

    /**
     * @param Closure(self):array{CasterInterface&MockObject, CharacterEncoding&MockObject} $factory
     * @param Closure(HTML5TableFormatter):HTML5TableFormatter $html5TableFormatterMutator
     */
    #[DataProvider('providerTestFormatWorks')]
    public function testFormatWorks(
        string $expectedJSONRegex,
        Closure $factory,
        Throwable $throwable,
        Closure $html5TableFormatterMutator,
    ): void {
        [$caster, $characterEncoding] = $factory($this);

        $html5TableFormatter = new HTML5TableFormatter($caster, $characterEncoding);
        $html5TableFormatter = $html5TableFormatterMutator($html5TableFormatter);

        $this->assertMatchesRegularExpression($expectedJSONRegex, $html5TableFormatter->format($throwable));
    }

    #[DataProvider('providerTestHtmlEncodeWorks')]
    public function testHtmlEncodeWorks(string $expected, string $text): void
    {
        $caster = $this->createMock(CasterInterface::class);
        $characterEncoding = $this->createMock(CharacterEncoding::class);

        $html5TableFormatter = new HTML5TableFormatter($caster, $characterEncoding);

        $this->assertSame($expected, $html5TableFormatter->htmlEncode($text));
    }

    #[DataProvider('providerTestHtmlEncodeWithLn2BrWorks')]
    public function testHtmlEncodeWithLn2BrWorks(string $expected, string $text): void
    {
        $caster = $this->createMock(CasterInterface::class);
        $characterEncoding = $this->createMock(CharacterEncoding::class);

        $html5TableFormatter = new HTML5TableFormatter($caster, $characterEncoding);

        $this->assertSame($expected, $html5TableFormatter->htmlEncodeWithLn2Br($text));
    }
}
