<?php

declare(strict_types = 1);

namespace Test\Unit\Eboreum\Exceptional\Formatting;

use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\Formatting\AbstractFormatter;
use Eboreum\Exceptional\Formatting\DefaultFormatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DefaultFormatterTest extends TestCase
{
    public function testBasics(): void
    {
        $caster = $this->_mockCasterInterface();

        $defaultFormatter = new DefaultFormatter($caster);

        $this->assertSame("    ", $defaultFormatter->getIndentationCharacters());
        $this->assertSame($caster, $defaultFormatter->getCaster());
    }

    /**
     * @dataProvider dataProvider_testFormatWorks
     */
    public function testFormatWorks(
        string $expectedJSONRegex,
        DefaultFormatter $defaultFormatter,
        \Throwable $throwable
    ): void
    {
        $this->assertMatchesRegularExpression($expectedJSONRegex, $defaultFormatter->format($throwable));
    }

    /**
     * @return array<int, array{0: string, 1: DefaultFormatter, 2: \Exception}>
     */
    public function dataProvider_testFormatWorks(): array
    {
        return [
            [
                sprintf(
                    implode("", [
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
                    preg_quote(basename(__FILE__), "/"),
                ),
                (function(){
                    $caster = $this->_mockCasterInterface();

                    $caster
                        ->expects($this->any())
                        ->method("maskString")
                        ->with($this->callback(function(string $v){
                            return (
                                "foo" === $v
                                || (1 === preg_match('/^#\d+ /', $v))
                            );
                        }))
                        ->will($this->returnCallback(function(string $v){
                            if ("foo" === $v) {
                                return "foo";
                            }

                            if (1 === preg_match('/^#\d+ /', $v)) {
                                return "#0 Lorem";
                            }

                            throw new \Exception(sprintf(
                                "Uncovered case for \$v = %s",
                                Caster::getInstance()->castTyped($v),
                            ));
                        }));

                    $defaultFormatter = new DefaultFormatter($caster);

                    return $defaultFormatter;
                })(),
                new \Exception("foo"),
            ],
            [
                sprintf(
                    implode("", [
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
                    preg_quote(basename(__FILE__), "/"),
                ),
                (function(){
                    $caster = $this->_mockCasterInterface();

                    $caster
                        ->expects($this->any())
                        ->method("maskString")
                        ->with($this->callback(function(string $v){
                            return (
                                "foo" === $v
                                || (1 === preg_match('/^#\d+ /', $v))
                            );
                        }))
                        ->will($this->returnCallback(function(string $v){
                            if ("foo" === $v) {
                                return "foo";
                            }

                            if (1 === preg_match('/^#\d+ /', $v)) {
                                return "#0 Lorem";
                            }

                            throw new \Exception(sprintf(
                                "Uncovered case for \$v = %s",
                                Caster::getInstance()->castTyped($v),
                            ));
                        }));

                    $defaultFormatter = new DefaultFormatter($caster);

                    $defaultFormatter = $defaultFormatter->withIsProvidingTimestamp(true);

                    return $defaultFormatter;
                })(),
                new \Exception("foo"),
            ],
            [
                sprintf(
                    implode("", [
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
                    preg_quote(basename(__FILE__), "/"),
                    preg_quote(basename(__FILE__), "/"),
                    preg_quote(basename(__FILE__), "/"),
                ),
                (function(){
                    $caster = $this->_mockCasterInterface();

                    $caster
                        ->expects($this->any())
                        ->method("maskString")
                        ->with($this->callback(function(string $v){
                            return (
                                in_array($v, ["foo", "bar", "baz"], true)
                                || (1 === preg_match('/^#\d+ /', $v))
                            );
                        }))
                        ->will($this->returnCallback(function(string $v){
                            if (in_array($v, ["foo", "bar", "baz"], true)) {
                                return $v;
                            }

                            if (1 === preg_match('/^#\d+ /', $v)) {
                                return "#0 Lorem";
                            }

                            throw new \Exception(sprintf(
                                "Uncovered case for \$v = %s",
                                Caster::getInstance()->castTyped($v),
                            ));
                        }));

                    $defaultFormatter = new DefaultFormatter($caster);

                    return $defaultFormatter;
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
                    preg_quote(basename(__FILE__), "/"),
                    preg_quote(basename(__FILE__), "/"),
                ),
                (function(){
                    $caster = $this->_mockCasterInterface();

                    $caster
                        ->expects($this->any())
                        ->method("maskString")
                        ->with($this->callback(function(string $v){
                            return (
                                in_array($v, ["foo", "bar", "baz", "bim"], true)
                                || (1 === preg_match('/^#\d+ /', $v))
                            );
                        }))
                        ->will($this->returnCallback(function(string $v){
                            if (in_array($v, ["foo", "bar", "baz", "bim"], true)) {
                                return $v;
                            }

                            if (1 === preg_match('/^#\d+ /', $v)) {
                                return "#0 Lorem";
                            }

                            throw new \Exception(sprintf(
                                "Uncovered case for \$v = %s",
                                Caster::getInstance()->castTyped($v),
                            ));
                        }));

                    $defaultFormatter = new DefaultFormatter($caster);

                    $defaultFormatter = $defaultFormatter->withMaximumPreviousDepth(1);

                    return $defaultFormatter;
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

    public function testWithIndentationCharactersWorks(): void
    {
        $caster = $this->_mockCasterInterface();

        $defaultFormatterA = new DefaultFormatter($caster);
        $defaultFormatterB = $defaultFormatterA->withIndentationCharacters("    ");
        $defaultFormatterC = $defaultFormatterA->withIndentationCharacters("+?+");

        $this->assertNotSame($defaultFormatterA, $defaultFormatterB);
        $this->assertNotSame($defaultFormatterA, $defaultFormatterC);
        $this->assertNotSame($defaultFormatterB, $defaultFormatterC);
        $this->assertSame("    ", $defaultFormatterA->getIndentationCharacters());
        $this->assertSame("    ", $defaultFormatterB->getIndentationCharacters());
        $this->assertSame("+?+", $defaultFormatterC->getIndentationCharacters());
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