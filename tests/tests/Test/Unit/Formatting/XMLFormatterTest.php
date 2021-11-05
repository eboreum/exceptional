<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional\Formatting;

use Eboreum\Caster\CharacterEncoding;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\Factory\PHPCore\SimpleXMLElement\SimpleXMLElementFactory;
use Eboreum\Exceptional\Formatting\AbstractFormatter;
use Eboreum\Exceptional\Formatting\AbstractXMLFormatter;
use Eboreum\Exceptional\Formatting\XMLFormatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class XMLFormatterTest extends TestCase
{
    public function testBasics(): void
    {
        $caster = $this->mockCasterInterface();
        $characterEncoding = $this->mockCharacterEncoding();

        $xmlFormatter = new XMLFormatter($caster, $characterEncoding);

        $this->assertSame($characterEncoding, $xmlFormatter->getCharacterEncoding());
        $this->assertSame(0, $xmlFormatter->getPreviousThrowableLevel());
        $this->assertSame($caster, $xmlFormatter->getCaster());
    }

    /**
     * @dataProvider dataProvider_testFormatWorks
     */
    public function testFormatWorks(string $expectedXMLRegex, XMLFormatter $xmlFormatter, \Throwable $throwable): void
    {
        $this->assertMatchesRegularExpression($expectedXMLRegex, $xmlFormatter->format($throwable));
    }

    /**
     * @return array<int, array{0: string, 1: XMLFormatter, 2: \Throwable}>
     */
    public function dataProvider_testFormatWorks(): array
    {
        return [
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\<\?xml version\="1\.0" encoding\="UTF\-8"\?\>\n',
                        '\<exception\>',
                            '\<class\>\\\\Exception\<\/class\>',
                            '\<file\>.+\/[^\/]+\/%s\<\/file\>',
                            '\<line\>\d+\<\/line\>',
                            '\<code\>0\<\/code\>',
                            '\<message\>foo\<\/message\>',
                            '\<stacktrace\>#0 Lorem\<\/stacktrace\>',
                            '\<previous\/\>',
                        '\<\/exception\>',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                (function () {
                    $caster = $this->mockCasterInterface();
                    $characterEncoding = $this->mockCharacterEncoding();

                    $characterEncoding
                        ->expects($this->any())
                        ->method('__toString')
                        ->with()
                        ->willReturn('UTF-8');

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

                    return new XMLFormatter($caster, $characterEncoding);
                })(),
                new \Exception('foo'),
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\<\?xml version\="1\.0" encoding\="UTF\-8"\?\>\n',
                        '\<error\>',
                            '\<class\>\\\\Error\<\/class\>',
                            '\<file\>.+\/[^\/]+\/%s\<\/file\>',
                            '\<line\>\d+\<\/line\>',
                            '\<code\>0\<\/code\>',
                            '\<message\>foo\<\/message\>',
                            '\<stacktrace\>#0 Lorem\<\/stacktrace\>',
                            '\<previous\/\>',
                        '\<\/error\>',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                (function () {
                    $caster = $this->mockCasterInterface();
                    $characterEncoding = $this->mockCharacterEncoding();

                    $characterEncoding
                        ->expects($this->any())
                        ->method('__toString')
                        ->with()
                        ->willReturn('UTF-8');

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

                    return new XMLFormatter($caster, $characterEncoding);
                })(),
                new \Error('foo'),
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\<\?xml version\="1\.0" encoding\="UTF\-8"\?\>',
                        '\n\<exception\>',
                        '\n  \<class\>\\\\Exception\<\/class\>',
                        '\n  \<file\>.+\/[^\/]+\/%s\<\/file\>',
                        '\n  \<line\>\d+\<\/line\>',
                        '\n  \<code\>0\<\/code\>',
                        '\n  \<message\>foo\<\/message\>',
                        '\n  \<stacktrace\>#0 Lorem\<\/stacktrace\>',
                        '\n  \<previous\/\>',
                        '\n\<\/exception\>',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                (function () {
                    $caster = $this->mockCasterInterface();
                    $characterEncoding = $this->mockCharacterEncoding();

                    $characterEncoding
                        ->expects($this->any())
                        ->method('__toString')
                        ->with()
                        ->willReturn('UTF-8');

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

                    $xmlFormatter = new XMLFormatter($caster, $characterEncoding);

                    /**
                     * @var XMLFormatter
                     */
                    $xmlFormatter = $xmlFormatter->withIsPrettyPrinting(true);

                    return $xmlFormatter;
                })(),
                new \Exception('foo'),
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\<\?xml version\="1\.0" encoding\="UTF\-8"\?\>\n',
                        '\<exception\>',
                            '\<class\>\\\\Exception\<\/class\>',
                            '\<time\>\d{4}\-\d{2}\-\d{2}T\d{2}\:\d{2}\:\d{2}[\+\-]\d{2}\:\d{2}\<\/time\>',
                            '\<file\>.+\/[^\/]+\/%s\<\/file\>',
                            '\<line\>\d+\<\/line\>',
                            '\<code\>0\<\/code\>',
                            '\<message\>foo\<\/message\>',
                            '\<stacktrace\>#0 Lorem\<\/stacktrace\>',
                            '\<previous\/\>',
                        '\<\/exception\>',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                (function () {
                    $caster = $this->mockCasterInterface();
                    $characterEncoding = $this->mockCharacterEncoding();

                    $characterEncoding
                        ->expects($this->any())
                        ->method('__toString')
                        ->with()
                        ->willReturn('UTF-8');

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

                    $xmlFormatter = new XMLFormatter($caster, $characterEncoding);

                    /**
                     * @var XMLFormatter
                     */
                    $xmlFormatter = $xmlFormatter->withIsProvidingTimestamp(true);

                    return $xmlFormatter;
                })(),
                new \Exception('foo'),
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\<\?xml version\="1\.0" encoding\="UTF\-8"\?\>\n',
                        '\<exception\>',
                            '\<class\>\\\\Exception\<\/class\>',
                            '\<file\>.+\/[^\/]+\/%s\<\/file\>',
                            '\<line\>\d+\<\/line\>',
                            '\<code\>0\<\/code\>',
                            '\<message\>foo\<\/message\>',
                            '\<stacktrace\>#0 Lorem\<\/stacktrace\>',
                            '\<previous\>',
                                '\<class\>\\\\RuntimeException\<\/class\>',
                                '\<file\>.+\/[^\/]+\/%s\<\/file\>',
                                '\<line\>\d+\<\/line\>',
                                '\<code\>1\<\/code\>',
                                '\<message\>bar\<\/message\>',
                                '\<stacktrace\>#0 .+\<\/stacktrace\>',
                                '\<previous\>',
                                    '\<class\>\\\\LogicException\<\/class\>',
                                    '\<file\>.+\/[^\/]+\/%s\<\/file\>',
                                    '\<line\>\d+\<\/line\>',
                                    '\<code\>2\<\/code\>',
                                    '\<message\>baz\<\/message\>',
                                    '\<stacktrace\>#0 .+\<\/stacktrace\>',
                                    '\<previous\/\>',
                                '\<\/previous\>',
                            '\<\/previous\>',
                        '\<\/exception\>',
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

                    $characterEncoding
                        ->expects($this->any())
                        ->method('__toString')
                        ->with()
                        ->willReturn('UTF-8');

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

                    return new XMLFormatter($caster, $characterEncoding);
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
                        '\<\?xml version\="1\.0" encoding\="UTF\-8"\?\>\n',
                        '\<exception\>',
                            '\<class\>\\\\Exception\<\/class\>',
                            '\<file\>.+\/[^\/]+\/%s\<\/file\>',
                            '\<line\>\d+\<\/line\>',
                            '\<code\>0\<\/code\>',
                            '\<message\>foo\<\/message\>',
                            '\<stacktrace\>#0 Lorem\<\/stacktrace\>',
                            '\<previous\>',
                                '\<class\>\\\\RuntimeException\<\/class\>',
                                '\<file\>.+\/[^\/]+\/%s\<\/file\>',
                                '\<line\>\d+\<\/line\>',
                                '\<code\>1\<\/code\>',
                                '\<message\>bar\<\/message\>',
                                '\<stacktrace\>#0 .+\<\/stacktrace\>',
                                '\<previous\>2 more \(omitted\)\<\/previous\>',
                            '\<\/previous\>',
                        '\<\/exception\>',
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

                    $characterEncoding
                        ->expects($this->any())
                        ->method('__toString')
                        ->with()
                        ->willReturn('UTF-8');

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

                    $xmlFormatter = new XMLFormatter($caster, $characterEncoding);

                    /**
                     * @var XMLFormatter
                     */
                    $xmlFormatter = $xmlFormatter->withMaximumPreviousDepth(1);

                    return $xmlFormatter;
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
                        '\<\?xml version\="1\.0" encoding\="UTF\-8"\?\>\n',
                        '\<exception\>',
                            '\<class\>\\\\Exception\<\/class\>',
                            '\<file\>.+\/[^\/]+\/%s\<\/file\>',
                            '\<line\>\d+\<\/line\>',
                            '\<code\>0\<\/code\>',
                            '\<message\>æøå\<\/message\>',
                            '\<stacktrace\>#0 Lorem\<\/stacktrace\>',
                            '\<previous\/\>',
                        '\<\/exception\>',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                (function () {
                    $caster = $this->mockCasterInterface();
                    $characterEncoding = $this->mockCharacterEncoding();

                    $characterEncoding
                        ->expects($this->any())
                        ->method('__toString')
                        ->with()
                        ->willReturn('UTF-8');

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

                    return new XMLFormatter($caster, $characterEncoding);
                })(),
                new \Exception('æøå'),
            ],
            [
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\<\?xml version\="1\.0" encoding\="UTF\-8"\?\>\n',
                        '\<lorem\>',
                            '\<class\>\\\\Exception\<\/class\>',
                            '\<file\>.+\/[^\/]+\/%s\<\/file\>',
                            '\<line\>\d+\<\/line\>',
                            '\<code\>0\<\/code\>',
                            '\<message\>foo\<\/message\>',
                            '\<stacktrace\>#0 Lorem\<\/stacktrace\>',
                            '\<previous\/\>',
                        '\<\/lorem\>',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                (function () {
                    $caster = $this->mockCasterInterface();
                    $characterEncoding = $this->mockCharacterEncoding();
                    $simpleXMLElementFactory = $this->mockSimpleXMLElementFactory();

                    $characterEncoding
                        ->expects($this->any())
                        ->method('__toString')
                        ->with()
                        ->willReturn('UTF-8');

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

                    $simpleXMLElementFactory
                        ->expects($this->exactly(1))
                        ->method('createSimpleXMLElement')
                        ->with('exception')
                        ->willReturn(new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><lorem></lorem>'));

                    $xmlFormatter = new XMLFormatter($caster, $characterEncoding);

                    /**
                     * @var XMLFormatter
                     */
                    $xmlFormatter = $xmlFormatter->withSimpleXMLElementFactory($simpleXMLElementFactory);

                    return $xmlFormatter;
                })(),
                new \Exception('foo'),
            ],
        ];
    }

    public function testFormatThrowsExceptionWhenCharacterEncodingIsNotSupported(): void
    {
        $caster = $this->mockCasterInterface();
        $characterEncoding = $this->mockCharacterEncoding();

        $characterEncoding
            ->expects($this->any())
            ->method('__toString')
            ->with()
            ->willReturn('');

        $xmlFormatter = new XMLFormatter($caster, $characterEncoding);

        try {
            $xmlFormatter->format(new \Exception('foo'));
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
                            ', \\\\%s\-\>\$isPrettyPrinting = \(bool\) false',
                            ', \\\\%s\-\>\$caster = \(object\) \\\\Mock_CasterInterface_[0-9a-f]{8}',
                            ', \\\\%s\-\>\$previousThrowableLevel = \(int\) 0',
                            ', \\\\%s\-\>\$maximumPreviousDepth = \(null\) null',
                            ', \\\\%s\-\>\$isProvidingTimestamp = \(bool\) false',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(XMLFormatter::class, '/'),
                    preg_quote(XMLFormatter::class, '/'),
                    preg_quote(AbstractXMLFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                    preg_quote(AbstractFormatter::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(\Exception::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'String could not be parsed as XML',
                    '$',
                    '/',
                ]),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'SimpleXMLElement\:\:__construct\(\)\: Entity: line 1\: parser error \: Invalid XML encoding name',
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

    public function testWithSimpleXMLElementFactoryWorks(): void
    {
        $caster = $this->mockCasterInterface();
        $characterEncoding = $this->mockCharacterEncoding();

        $xmlFormatterA = new XMLFormatter($caster, $characterEncoding);
        $xmlFormatterB = $xmlFormatterA->withSimpleXMLElementFactory(null);

        $simpleXMLElementFactoryC = $this->mockSimpleXMLElementFactory();

        $xmlFormatterC = $xmlFormatterA->withSimpleXMLElementFactory($simpleXMLElementFactoryC);

        $this->assertNotSame($xmlFormatterA, $xmlFormatterB);
        $this->assertNotSame($xmlFormatterA, $xmlFormatterC);
        $this->assertNotSame($xmlFormatterB, $xmlFormatterC);
        $this->assertSame(null, $xmlFormatterA->getSimpleXMLElementFactory());
        $this->assertSame(null, $xmlFormatterB->getSimpleXMLElementFactory());
        $this->assertSame($simpleXMLElementFactoryC, $xmlFormatterC->getSimpleXMLElementFactory());
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

    /**
     * @return SimpleXMLElementFactory&MockObject
     */
    private function mockSimpleXMLElementFactory(): SimpleXMLElementFactory
    {
        return $this
            ->getMockBuilder(SimpleXMLElementFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}