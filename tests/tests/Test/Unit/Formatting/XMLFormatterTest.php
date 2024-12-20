<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional\Formatting;

use Closure;
use Eboreum\Caster\CharacterEncoding;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\Factory\PHPCore\SimpleXMLElement\SimpleXMLElementFactory;
use Eboreum\Exceptional\Formatting\AbstractFormatter;
use Eboreum\Exceptional\Formatting\AbstractXMLFormatter;
use Eboreum\Exceptional\Formatting\XMLFormatter;
use Eboreum\PhpunitWithConsecutiveAlternative\MethodCallExpectation;
use Error;
use Exception;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use SimpleXMLElement;
use Test\Unit\Eboreum\Exceptional\AbstractTestCase;
use Throwable;

use function assert;
use function basename;
use function implode;
use function is_object;
use function preg_match;
use function preg_quote;
use function sprintf;

#[CoversClass(XMLFormatter::class)]
class XMLFormatterTest extends AbstractTestCase
{
    /**
     * @return array<
     *   int,
     *   array{
     *     string,
     *     Closure(self):array{CasterInterface&MockObject, CharacterEncoding&MockObject},
     *     Throwable,
     *     Closure(self, XMLFormatter):XMLFormatter,
     * }>
     */
    public static function providerTestFormatWorks(): array
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
                static function (self $self): array {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $characterEncoding
                        ->expects($self->any())
                        ->method('__toString')
                        ->with()
                        ->willReturn('UTF-8');

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
                static function (self $self, XMLFormatter $xmlFormatter): XMLFormatter {
                    return $xmlFormatter;
                },
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
                static function (self $self): array {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $characterEncoding
                        ->expects($self->any())
                        ->method('__toString')
                        ->with()
                        ->willReturn('UTF-8');

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
                new Error('foo'),
                static function (self $self, XMLFormatter $xmlFormatter): XMLFormatter {
                    return $xmlFormatter;
                },
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
                static function (self $self): array {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $characterEncoding
                        ->expects($self->any())
                        ->method('__toString')
                        ->with()
                        ->willReturn('UTF-8');

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
                static function (self $self, XMLFormatter $xmlFormatter): XMLFormatter {
                    return $xmlFormatter->withIsPrettyPrinting(true);
                },
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
                static function (self $self): array {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $characterEncoding
                        ->expects($self->any())
                        ->method('__toString')
                        ->with()
                        ->willReturn('UTF-8');

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
                static function (self $self, XMLFormatter $xmlFormatter): XMLFormatter {
                    return $xmlFormatter->withIsProvidingTimestamp(true);
                },
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
                static function (self $self): array {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $characterEncoding
                        ->expects($self->any())
                        ->method('__toString')
                        ->with()
                        ->willReturn('UTF-8');

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
                static function (self $self, XMLFormatter $xmlFormatter): XMLFormatter {
                    return $xmlFormatter;
                },
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
                ),
                static function (self $self): array {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $characterEncoding
                        ->expects($self->any())
                        ->method('__toString')
                        ->with()
                        ->willReturn('UTF-8');

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
                static function (self $self, XMLFormatter $xmlFormatter): XMLFormatter {
                    return $xmlFormatter->withMaximumPreviousDepth(1);
                },
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
                static function (self $self): array {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $characterEncoding
                        ->expects($self->any())
                        ->method('__toString')
                        ->with()
                        ->willReturn('UTF-8');

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
                static function (self $self, XMLFormatter $xmlFormatter): XMLFormatter {
                    return $xmlFormatter;
                },
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
                static function (self $self): array {
                    $caster = $self->createMock(CasterInterface::class);
                    $characterEncoding = $self->createMock(CharacterEncoding::class);

                    $characterEncoding
                        ->expects($self->any())
                        ->method('__toString')
                        ->with()
                        ->willReturn('UTF-8');

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
                static function (self $self, XMLFormatter $xmlFormatter): XMLFormatter {
                    $simpleXMLElementFactory = $self->createMock(SimpleXMLElementFactory::class);

                    $simpleXMLElementFactory
                        ->expects($self->exactly(1))
                        ->method('createSimpleXMLElement')
                        ->with('exception')
                        ->willReturn(new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><lorem></lorem>'));

                    return $xmlFormatter->withSimpleXMLElementFactory($simpleXMLElementFactory);
                },
            ],
        ];
    }

    public function testBasics(): void
    {
        $caster = $this->createMock(CasterInterface::class);
        $characterEncoding = $this->createMock(CharacterEncoding::class);

        $xmlFormatter = new XMLFormatter($caster, $characterEncoding);

        $this->assertSame($characterEncoding, $xmlFormatter->getCharacterEncoding());
        $this->assertSame(0, $xmlFormatter->getPreviousThrowableLevel());
        $this->assertSame($caster, $xmlFormatter->getCaster());
    }

    /**
     * @param Closure(self):array{CasterInterface&MockObject, CharacterEncoding&MockObject} $factory
     * @param Closure(self, XMLFormatter):XMLFormatter $xmlFormatterMutator
     */
    #[DataProvider('providerTestFormatWorks')]
    public function testFormatWorks(
        string $expectedXMLRegex,
        Closure $factory,
        Throwable $throwable,
        Closure $xmlFormatterMutator,
    ): void {
        [$caster, $characterEncoding] = $factory($this);

        $xmlFormatter = new XMLFormatter($caster, $characterEncoding);
        $xmlFormatter = $xmlFormatterMutator($this, $xmlFormatter);

        $this->assertMatchesRegularExpression($expectedXMLRegex, $xmlFormatter->format($throwable));
    }

    public function testFormatThrowsExceptionWhenCharacterEncodingIsNotSupported(): void
    {
        $caster = $this->createMock(CasterInterface::class);
        $characterEncoding = $this->createMock(CharacterEncoding::class);

        $characterEncoding
            ->expects($this->any())
            ->method('__toString')
            ->with()
            ->willReturn('');

        $xmlFormatter = new XMLFormatter($caster, $characterEncoding);

        try {
            $xmlFormatter->format(new Exception('foo'));
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
                            ', \\\\%s\-\>\$isPrettyPrinting = \(bool\) false',
                            ', \\\\%s\-\>\$caster = \(object\) \\\\MockObject_CasterInterface_[0-9a-f]{8}',
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
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(Exception::class, $currentException::class);
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
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testWithSimpleXMLElementFactoryWorks(): void
    {
        $caster = $this->createMock(CasterInterface::class);
        $characterEncoding = $this->createMock(CharacterEncoding::class);

        $xmlFormatterA = new XMLFormatter($caster, $characterEncoding);
        $xmlFormatterB = $xmlFormatterA->withSimpleXMLElementFactory(null);

        $simpleXMLElementFactoryC = $this->createMock(SimpleXMLElementFactory::class);

        $xmlFormatterC = $xmlFormatterA->withSimpleXMLElementFactory($simpleXMLElementFactoryC);

        $this->assertNotSame($xmlFormatterA, $xmlFormatterB);
        $this->assertNotSame($xmlFormatterA, $xmlFormatterC);
        $this->assertNotSame($xmlFormatterB, $xmlFormatterC);
        $this->assertSame(null, $xmlFormatterA->getSimpleXMLElementFactory());
        $this->assertSame(null, $xmlFormatterB->getSimpleXMLElementFactory());
        $this->assertSame($simpleXMLElementFactoryC, $xmlFormatterC->getSimpleXMLElementFactory());
    }
}
