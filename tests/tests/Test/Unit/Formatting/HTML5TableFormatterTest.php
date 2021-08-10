<?php

declare(strict_types = 1);

namespace Test\Unit\Eboreum\Exceptional\Formatting;

use Eboreum\Caster\CharacterEncoding;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\Formatting\AbstractFormatter;
use Eboreum\Exceptional\Formatting\HTML5TableFormatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HTML5TableFormatterTest extends TestCase
{
    public function testBasics(): void
    {
        $caster = $this->_mockCasterInterface();
        $characterEncoding = $this->_mockCharacterEncoding();

        $html5TableFormatter = new HTML5TableFormatter($caster, $characterEncoding);

        $this->assertSame($caster, $html5TableFormatter->getCaster());
        $this->assertSame($characterEncoding, $html5TableFormatter->getCharacterEncoding());
    }

    /**
     * @dataProvider dataProvider_testFormatWorks
     */
    public function testFormatWorks(
        string $expectedJSONRegex,
        HTML5TableFormatter $html5TableFormatter,
        \Throwable $throwable
    ): void
    {
        $this->assertMatchesRegularExpression($expectedJSONRegex, $html5TableFormatter->format($throwable));
    }

    /**
     * @return array<int, array{0: string, 1: HTML5TableFormatter, 2: \Exception}>
     */
    public function dataProvider_testFormatWorks(): array
    {
        return [
            [
                sprintf(
                    implode("", [
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
                    preg_quote(basename(__FILE__), "/"),
                ),
                (function(){
                    $caster = $this->_mockCasterInterface();
                    $characterEncoding = $this->_mockCharacterEncoding();

                    $caster
                        ->expects($this->exactly(2))
                        ->method("maskString")
                        ->withConsecutive(
                            ["foo"],
                            [
                                $this->callback(function(string $v){
                                    return (1 === preg_match('/^#\d+ /', $v));
                                }),
                            ],
                        )
                        ->willReturnOnConsecutiveCalls(
                            "foo",
                            "#0 Lorem",
                        );

                    $html5TableFormatter = new HTML5TableFormatter($caster, $characterEncoding);

                    return $html5TableFormatter;
                })(),
                new \Exception("foo"),
            ],
            [
                sprintf(
                    implode("", [
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
                    preg_quote(basename(__FILE__), "/"),
                ),
                (function(){
                    $caster = $this->_mockCasterInterface();
                    $characterEncoding = $this->_mockCharacterEncoding();

                    $caster
                        ->expects($this->exactly(2))
                        ->method("maskString")
                        ->withConsecutive(
                            ["foo"],
                            [
                                $this->callback(function(string $v){
                                    return (1 === preg_match('/^#\d+ /', $v));
                                }),
                            ],
                        )
                        ->willReturnOnConsecutiveCalls(
                            "foo",
                            "#0 Lorem",
                        );

                    $html5TableFormatter = new HTML5TableFormatter($caster, $characterEncoding);

                    $html5TableFormatter = $html5TableFormatter->withIsPrettyPrinting(true);

                    return $html5TableFormatter;
                })(),
                new \Exception("foo"),
            ],
            [
                sprintf(
                    implode("", [
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
                    preg_quote(basename(__FILE__), "/"),
                ),
                (function(){
                    $caster = $this->_mockCasterInterface();
                    $characterEncoding = $this->_mockCharacterEncoding();

                    $caster
                        ->expects($this->exactly(2))
                        ->method("maskString")
                        ->withConsecutive(
                            ['<p class="mellon">foo</p>'],
                            [
                                $this->callback(function(string $v){
                                    return (1 === preg_match('/^#\d+ /', $v));
                                }),
                            ],
                        )
                        ->willReturnOnConsecutiveCalls(
                            '<p class="mellon">foo</p>',
                            "#0 Lorem",
                        );

                    $html5TableFormatter = new HTML5TableFormatter($caster, $characterEncoding);

                    return $html5TableFormatter;
                })(),
                new \Exception('<p class="mellon">foo</p>'),
            ],
            [
                sprintf(
                    implode("", [
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
                    preg_quote(basename(__FILE__), "/"),
                ),
                (function(){
                    $caster = $this->_mockCasterInterface();
                    $characterEncoding = $this->_mockCharacterEncoding();

                    $caster
                        ->expects($this->exactly(2))
                        ->method("maskString")
                        ->withConsecutive(
                            ["foo"],
                            [
                                $this->callback(function(string $v){
                                    return (1 === preg_match('/^#\d+ /', $v));
                                }),
                            ],
                        )
                        ->willReturnOnConsecutiveCalls(
                            "foo",
                            "#0 Lorem",
                        );

                    $html5TableFormatter = new HTML5TableFormatter($caster, $characterEncoding);

                    $html5TableFormatter = $html5TableFormatter->withIsProvidingTimestamp(true);

                    return $html5TableFormatter;
                })(),
                new \Exception("foo"),
            ],
            [
                sprintf(
                    implode("", [
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
                    preg_quote(basename(__FILE__), "/"),
                    preg_quote(basename(__FILE__), "/"),
                    preg_quote(basename(__FILE__), "/"),
                ),
                (function(){
                    $caster = $this->_mockCasterInterface();
                    $characterEncoding = $this->_mockCharacterEncoding();

                    $caster
                        ->expects($this->exactly(6))
                        ->method("maskString")
                        ->withConsecutive(
                            ["foo"],
                            [
                                $this->callback(function(string $v){
                                    return (1 === preg_match('/^#\d+ /', $v));
                                }),
                            ],
                            ["bar"],
                            [
                                $this->callback(function(string $v){
                                    return (1 === preg_match('/^#\d+ /', $v));
                                }),
                            ],
                            ["baz"],
                            [
                                $this->callback(function(string $v){
                                    return (1 === preg_match('/^#\d+ /', $v));
                                }),
                            ],
                        )
                        ->willReturnOnConsecutiveCalls(
                            "foo",
                            "#0 Lorem",
                            "bar",
                            "#0 Lorem",
                            "baz",
                            "#0 Lorem",
                        );

                    $html5TableFormatter = new HTML5TableFormatter($caster, $characterEncoding);

                    return $html5TableFormatter;
                })(),
                (function(){
                    $baz = new \LogicException("baz", 2);
                    $bar = new \RuntimeException("bar", 1, $baz);

                    return new \Exception("foo", 0, $bar);
                })(),
            ],
            [
                sprintf(
                    implode("", [
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
                    preg_quote(basename(__FILE__), "/"),
                    preg_quote(basename(__FILE__), "/"),
                    preg_quote(basename(__FILE__), "/"),
                ),
                (function(){
                    $caster = $this->_mockCasterInterface();
                    $characterEncoding = $this->_mockCharacterEncoding();

                    $caster
                        ->expects($this->exactly(4))
                        ->method("maskString")
                        ->withConsecutive(
                            ["foo"],
                            [
                                $this->callback(function(string $v){
                                    return (1 === preg_match('/^#\d+ /', $v));
                                }),
                            ],
                            ["bar"],
                            [
                                $this->callback(function(string $v){
                                    return (1 === preg_match('/^#\d+ /', $v));
                                }),
                            ],
                        )
                        ->willReturnOnConsecutiveCalls(
                            "foo",
                            "#0 Lorem",
                            "bar",
                            "#0 Lorem",
                        );

                    $html5TableFormatter = new HTML5TableFormatter($caster, $characterEncoding);

                    $html5TableFormatter = $html5TableFormatter->withMaximumPreviousDepth(1);

                    return $html5TableFormatter;
                })(),
                (function(){
                    $bim = new \LogicException("bim", 3);
                    $baz = new \LogicException("baz", 2, $bim);
                    $bar = new \RuntimeException("bar", 1, $baz);

                    return new \Exception("foo", 0, $bar);
                })(),
            ],
        ];
    }

    /**
     * @dataProvider dataProvider_testHtmlEncodeWorks
     */
    public function testHtmlEncodeWorks(
        string $expected,
        string $text
    ): void
    {
        $caster = $this->_mockCasterInterface();
        $characterEncoding = $this->_mockCharacterEncoding();

        $html5TableFormatter = new HTML5TableFormatter($caster, $characterEncoding);

        $this->assertSame($expected, $html5TableFormatter->htmlEncode($text));
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    public function dataProvider_testHtmlEncodeWorks(): array
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
     * @dataProvider dataProvider_testHtmlEncodeWithLn2BrWorks
     */
    public function testHtmlEncodeWithLn2BrWorks(
        string $expected,
        string $text
    ): void
    {
        $caster = $this->_mockCasterInterface();
        $characterEncoding = $this->_mockCharacterEncoding();

        $html5TableFormatter = new HTML5TableFormatter($caster, $characterEncoding);

        $this->assertSame($expected, $html5TableFormatter->htmlEncodeWithLn2Br($text));
    }

        /**
     * @return array<int, array{0: string, 1: string}>
     */
    public function dataProvider_testHtmlEncodeWithLn2BrWorks(): array
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
                "foo<br>bar",
                "foo\nbar",
            ],
            [
                "foo<br><br><br>bar",
                "foo\n\n\nbar",
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

    /**
     * @return CharacterEncoding&MockObject
     */
    private function _mockCharacterEncoding(): CharacterEncoding
    {
        return $this
            ->getMockBuilder(CharacterEncoding::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}