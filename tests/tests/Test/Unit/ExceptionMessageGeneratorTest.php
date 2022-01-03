<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional;

use Eboreum\Caster\Common\DataType\Integer\PositiveInteger;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\ExceptionMessageGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest\testMakeFailureInMethodMessageWorks_ANamedClassWhereMethodSignatureChangesBetweenUppermostClassAndParentClasses_A;
use TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest\testMakeFailureInMethodMessageWorks_ANamedClassWhereMethodSignatureChangesBetweenUppermostClassAndParentClasses_C;
use TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest\testMakeFailureInMethodMessageWorks_ClassANoNamedArguments;
use TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest\testMakeFailureInMethodMessageWorks_ClassB4NamedArguments;
use TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest\testMakeFailureInMethodMessageWorksWithNonStaticMethods_AClassWithADefaultConstant;
use TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest\testMakeUninitializedPropertySafeToTextualIdentifierStringThrowsExceptionWhenPropertiesDoNotExist_ClassA;
use TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest\testMakeUninitializedPropertySafeToTextualIdentifierStringThrowsExceptionWhenPropertiesDoNotExist_ClassB;
use TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest\testMakeUninitializedPropertySafeToTextualIdentifierStringThrowsExceptionWhenPropertiesDoNotExist_ClassBParent;
use TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest\testMakeUninitializedPropertySafeToTextualIdentifierStringWorks_ClassA;
use TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest\testMakeUninitializedPropertySafeToTextualIdentifierStringWorks_ClassB;
use TestResource\Unit\Eboreum\Exceptional\ExceptionMessageGeneratorTest\testMakeUninitializedPropertySafeToTextualIdentifierStringWorks_ClassBParent;

class ExceptionMessageGeneratorTest extends TestCase
{
    public function testBasics(): void
    {
        $caster = Caster::create();
        $exceptionMessageGenerator = new ExceptionMessageGenerator($caster);

        $this->assertSame($caster, $exceptionMessageGenerator->getCaster());
    }

    /**
     * @dataProvider dataProvider_testCastFunctionArgumentsToStringWorks
     * @param array<int, mixed> $arguments
     */
    public function testCastFunctionArgumentsToStringWorks(
        string $message,
        string $expected,
        \Closure $reflectionFunctionFactory,
        array $arguments,
        ExceptionMessageGenerator $exceptionMessageGenerator
    ): void {
        $reflectionFunction = $reflectionFunctionFactory();

        $this->assertSame(
            $expected,
            $exceptionMessageGenerator->castFunctionArgumentsToString(
                $reflectionFunction,
                $arguments,
            ),
            $message,
        );
    }

    /**
     * @return array<int, array{string, string, \Closure, array<int, mixed>, ExceptionMessageGenerator}>
     */
    public function dataProvider_testCastFunctionArgumentsToStringWorks(): array
    {
        return [
            [
                '`stripos` called with 2 arguments.',
                '$haystack = , $needle = , $offset = ',
                static function (): \ReflectionFunction {
                    return new \ReflectionFunction('stripos');
                },
                [
                    'Lorem ipsum',
                    'ip',
                ],
                new ExceptionMessageGenerator($this->mockCasterInterface()),
            ],
            [
                '`stripos` called with 3 arguments.',
                '$haystack = foo, $needle = bar, $offset = 42',
                static function (): \ReflectionFunction {
                    return new \ReflectionFunction('stripos');
                },
                [
                    'Lorem ipsum',
                    'ip',
                ],
                (function (): ExceptionMessageGenerator {
                    $caster = $this->mockCasterInterface();

                    $caster
                        ->expects($this->exactly(1))
                        ->method('withDepthCurrent')
                        ->with($this->callback(static function ($v): bool {
                            return (
                                is_object($v)
                                && $v instanceof PositiveInteger
                            );
                        }))
                        ->willReturn($caster);

                    $caster
                        ->expects($this->exactly(3))
                        ->method('castTyped')
                        ->withConsecutive(
                            ['Lorem ipsum'],
                            ['ip'],
                            [0],
                        )
                        ->willReturnOnConsecutiveCalls(
                            'foo',
                            'bar',
                            '42',
                        );

                    return new ExceptionMessageGenerator($caster);
                })(),
            ],
            [
                'Method in anonymous class. 2 named arguments. 1st optional, 2nd required and variadic. Called with 4 arguments; no splat operator.',
                '$a = 42, $b = ...foo,bar,baz',
                static function (): \ReflectionMethod {
                    $object = new class
                    {
                        public function foo(int $a = 42, string ...$b): void
                        {
                        }
                    };

                    return new \ReflectionMethod($object, 'foo');
                },
                [
                    42,
                    'foo',
                    'bar',
                    'baz',
                ],
                (function (): ExceptionMessageGenerator {
                    $caster = $this->mockCasterInterface();

                    $caster
                        ->expects($this->exactly(1))
                        ->method('withDepthCurrent')
                        ->with($this->callback(static function ($v): bool {
                            return (
                                is_object($v)
                                && $v instanceof PositiveInteger
                            );
                        }))
                        ->willReturn($caster);

                    $caster
                        ->expects($this->any())
                        ->method('castTyped')
                        ->withConsecutive(
                            [42],
                            [
                                [
                                    'foo',
                                    'bar',
                                    'baz',
                                ]
                            ],
                        )
                        ->willReturnOnConsecutiveCalls(
                            '42',
                            'foo,bar,baz',
                        );

                    return new ExceptionMessageGenerator($caster);
                })(),
            ],
            [
                'Method in anonymous class. 2 named arguments. 1st optional. 2nd required and variadic. Called with 1 argument; with splat operator.',
                '$a = 42, $b = ...[]',
                static function (): \ReflectionMethod {
                    $object = new class
                    {
                        public function foo(int $a = 42, string ...$b): void
                        {
                        }
                    };

                    return new \ReflectionMethod($object, 'foo');
                },
                [42],
                (function (): ExceptionMessageGenerator {
                    $caster = $this->mockCasterInterface();

                    $caster
                        ->expects($this->exactly(1))
                        ->method('withDepthCurrent')
                        ->with($this->callback(static function ($v): bool {
                            return (
                                is_object($v)
                                && $v instanceof PositiveInteger
                            );
                        }))
                        ->willReturn($caster);

                    $caster
                        ->expects($this->any())
                        ->method('castTyped')
                        ->withConsecutive(
                            [42],
                            [[]],
                        )
                        ->willReturnOnConsecutiveCalls(
                            '42',
                            '[]',
                        );

                    return new ExceptionMessageGenerator($caster);
                })(),
            ],
            [
                'Method in anonymous class. 2 named arguments. 1st optional. 2nd is required and variadic. Called with 1 argument.',
                '$a = 42, $b = ...[]',
                static function (): \ReflectionMethod {
                    $object = new class
                    {
                        public function foo(int $a = 42, string ...$b): void
                        {
                        }
                    };

                    return new \ReflectionMethod($object, 'foo');
                },
                [42],
                (function (): ExceptionMessageGenerator {
                    $caster = $this->mockCasterInterface();

                    $caster
                        ->expects($this->exactly(1))
                        ->method('withDepthCurrent')
                        ->with($this->callback(static function ($v): bool {
                            return (
                                is_object($v)
                                && $v instanceof PositiveInteger
                            );
                        }))
                        ->willReturn($caster);

                    $caster
                        ->expects($this->any())
                        ->method('castTyped')
                        ->withConsecutive(
                            [42],
                            [],
                        )
                        ->willReturnOnConsecutiveCalls(
                            '42',
                            '[]',
                        );

                    return new ExceptionMessageGenerator($caster);
                })(),
            ],
        ];
    }

    public function testCastFunctionArgumentsToStringThrowsExceptionWhenArgumentReflectionFunctionIsNotAccepted(): void
    {
        $exceptionMessageGenerator = new ExceptionMessageGenerator($this->mockCasterInterface());
        $reflectionFunction = new class extends \ReflectionFunctionAbstract
        {
            public function __toString(): string
            {
                return '';
            }
        };

        try {
            $exceptionMessageGenerator->castFunctionArgumentsToString($reflectionFunction, []);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>castFunctionArgumentsToString\(',
                            '\$reflectionFunction = \(object\) class@anonymous\/in\/.+\/%s:\d+',
                            ', \$functionArgumentValues = ',
                        '\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(ExceptionMessageGenerator::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'A \$discloser was not produced from \$reflectionFunction = \(object\)',
                        ' class@anonymous\/in\/.+\/%s:\d+',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testCastFunctionArgumentsToStringHandleExceptionGracefullyForReflectionMethod(): void
    {
        $caster = $this->mockCasterInterface();
        $exception = new \Exception('foo');

        $caster
            ->expects($this->exactly(1))
            ->method('withDepthCurrent')
            ->willThrowException($exception);

        $exceptionMessageGenerator = new ExceptionMessageGenerator($caster);
        $object = new class
        {
            public function foo(): string
            {
                return '';
            }
        };
        $reflectionMethod = new \ReflectionMethod($object, 'foo');

        try {
            $exceptionMessageGenerator->castFunctionArgumentsToString($reflectionMethod, []);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>castFunctionArgumentsToString\(',
                            '\$reflectionFunction = \(object\) \\\\ReflectionMethod \(',
                                'declaring class: class@anonymous\/in\/.+\/%s:\d+',
                            '\)',
                            ', \$functionArgumentValues = ',
                        '\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(ExceptionMessageGenerator::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame($exception, $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    /**
     * @dataProvider dataProvider_testMakeFailureInFunctionMessageWorksForAnonymousFunctions
     */
    public function testMakeFailureInFunctionMessageWorksForAnonymousFunctions(
        string $message,
        string $expectedRegex,
        \Closure $callback
    ): void {
        [
            $reflectionFunction,
            $functionArgumentValues,
        ] = $callback();

        $caster = Caster::create();
        $exceptionMessageGenerator = new ExceptionMessageGenerator($caster);

        $this->assertMatchesRegularExpression(
            $expectedRegex,
            $exceptionMessageGenerator->makeFailureInFunctionMessage($reflectionFunction, $functionArgumentValues),
            $message,
        );
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: object}>
     */
    public function dataProvider_testMakeFailureInFunctionMessageWorksForAnonymousFunctions(): array
    {
        return [
            [
                'An anonymous function, no named arguments exist, no arguments are passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in closure\/anonymous function defined in %s:\d+',
                        ', called with 0 arguments and actually having arguments\: \(\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(__FILE__, '/'),
                ),
                static function () {
                    $anonymousFunction = static function () use (&$anonymousFunction): array {
                        return [
                            new \ReflectionFunction($anonymousFunction),
                            func_get_args(),
                        ];
                    };

                    return $anonymousFunction();
                },
            ],
            [
                'An anonymous function, no named arguments exist, 3 arguments are passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in closure\/anonymous function defined in %s:\d+',
                        ', called with 3 arguments and actually having arguments\: \(',
                            '\{0\} \= \(int\) 42',
                            ', \{1\} \= \(string\(3\)\) "foo"',
                            ', \{2\} \= \(float\) 3\.14',
                        '\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(__FILE__, '/'),
                ),
                static function () {
                    $anonymousFunction = static function () use (&$anonymousFunction): array {
                        return [
                            new \ReflectionFunction($anonymousFunction),
                            func_get_args(),
                        ];
                    };

                    return $anonymousFunction(42, 'foo', 3.14); // @phpstan-ignore-line
                },
            ],
            [
                'An anonymous function, 1 named argument with default, 0 arguments are passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in closure\/anonymous function defined in %s:\d+',
                        ', called with 0 arguments and actually having arguments\: \(',
                            '\$a \= \(int\) 42',
                        '\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(__FILE__, '/'),
                ),
                static function () {
                    $anonymousFunction = static function (int $a = 42) use (&$anonymousFunction): array {
                        return [
                            new \ReflectionFunction($anonymousFunction),
                            func_get_args(),
                        ];
                    };

                    return $anonymousFunction();
                },
            ],
            [
                'An anonymous function, 1 named argument with default, 1 argument is passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in closure\/anonymous function defined in %s:\d+',
                        ', called with 1 argument and actually having arguments\: \(',
                            '\$a \= \(int\) 43',
                        '\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(__FILE__, '/'),
                ),
                static function () {
                    $anonymousFunction = static function (int $a = 42) use (&$anonymousFunction): array {
                        return [
                            new \ReflectionFunction($anonymousFunction),
                            func_get_args(),
                        ];
                    };

                    return $anonymousFunction(43);
                },
            ],
            [
                'An anonymous function, 3 named arguments with defaults, 0 arguments are passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in closure\/anonymous function defined in %s:\d+',
                        ', called with 0 arguments and actually having arguments\: \(',
                            '\$a \= \(int\) 42',
                            ', \$b \= \(string\(3\)\) "foo"',
                            ', \$c \= \(float\) 3\.14',
                        '\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(__FILE__, '/'),
                ),
                static function () {
                    $anonymousFunction = static function (
                        int $a = 42,
                        string $b = 'foo',
                        float $c = 3.14
                    ) use (&$anonymousFunction): array {
                        return [
                            new \ReflectionFunction($anonymousFunction),
                            func_get_args(),
                        ];
                    };

                    return $anonymousFunction();
                },
            ],
            [
                'An anonymous function, 3 named arguments with defaults, 2 arguments are passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in closure\/anonymous function defined in %s:\d+',
                        ', called with 2 arguments and actually having arguments\: \(',
                            '\$a \= \(int\) 43',
                            ', \$b \= \(string\(3\)\) "bar"',
                            ', \$c \= \(float\) 3\.14',
                        '\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(__FILE__, '/'),
                ),
                static function () {
                    $anonymousFunction = static function (
                        int $a = 42,
                        string $b = 'foo',
                        float $c = 3.14
                    ) use (&$anonymousFunction): array {
                        return [
                            new \ReflectionFunction($anonymousFunction),
                            func_get_args(),
                        ];
                    };

                    return $anonymousFunction(43, 'bar');
                },
            ],
        ];
    }

    public function testMakeFailureInFunctionMessageWorksForANamedFunction(): void
    {
        $reflectionFunction = new \ReflectionFunction('strpos');

        $this->assertMatchesRegularExpression(
            sprintf(
                implode('', [
                    '/',
                    '^',
                    'Failure in function \\\\strpos\(',
                        '\$haystack \= \(string\(9\)\) "foobarbaz"',
                        ', \$needle \= \(string\(3\)\) "bar"',
                        ', \$offset \= (',
                            '(\(null\) null)', // PHP 7.4
                            '|',
                            '(\(int\) 0)', // PHP 8.0
                        ')',
                    '\)',
                    '$',
                    '/',
                ]),
            ),
            ExceptionMessageGenerator::getInstance()->makeFailureInFunctionMessage(
                $reflectionFunction,
                ['foobarbaz', 'bar'],
            ),
        );
    }

    public function testMakeFailureInFunctionMessageHandlesExceptionGracefully(): void
    {
        $reflectionFunction = $this
            ->getMockBuilder('ReflectionFunction')
            ->disableOriginalConstructor()
            ->getMock();

        $exception = new \Exception();

        $reflectionFunction
            ->expects($this->exactly(1))
            ->method('isClosure')
            ->with()
            ->willThrowException($exception);

        try {
            ExceptionMessageGenerator::getInstance()->makeFailureInFunctionMessage(
                $reflectionFunction,
                [],
            );
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s\-\>makeFailureInFunctionMessage\(',
                            '\$reflectionFunction \= \(object\) \\\\Mock_ReflectionFunction_[0-9a-f]{8}',
                            ', \$functionArgumentValues \= \(array\(0\)\) \[\]',
                        '\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(ExceptionMessageGenerator::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame($exception, $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    /**
     * @dataProvider dataProvider_testMakeFailureInMethodMessageWorksWithNonStaticMethods
     */
    public function testMakeFailureInMethodMessageWorksWithNonStaticMethods(
        string $message,
        string $expectedRegex,
        \Closure $objectFactory
    ): void {
        $this->assertMatchesRegularExpression(
            $expectedRegex,
            (string)$objectFactory(),
            $message,
        );
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: object}>
     */
    public function dataProvider_testMakeFailureInMethodMessageWorksWithNonStaticMethods(): array
    {
        return [
            [
                'An anonymous class, no named arguments exist, no arguments are passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in (class@anonymous\/in\/.+\/%s:\d+)',
                        '\-\>__construct\(\) inside \(object\) \1',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function () {
                    return new class
                    {
                        private string $message;

                        public function __construct()
                        {
                            $this->message = ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                                $this,
                                new \ReflectionMethod(__CLASS__, __FUNCTION__),
                                func_get_args(),
                            );
                        }

                        public function __toString(): string
                        {
                            return $this->message;
                        }
                    };
                },
            ],
            [
                'An anonymous class, no named arguments exist, no arguments are passed, static::class used instead of $this',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in (class@anonymous\/in\/.+\/%s:\d+)',
                        '\-\>__construct\(\) inside \(object\) \1',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function () {
                    return new class
                    {
                        private string $message;

                        public function __construct()
                        {
                            $this->message = ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                                static::class,
                                new \ReflectionMethod(__CLASS__, __FUNCTION__),
                                func_get_args(),
                            );
                        }

                        public function __toString(): string
                        {
                            return $this->message;
                        }
                    };
                },
            ],
            [
                'An anonymous class, no named arguments exist, 1 argument is passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in class@anonymous\/in\/.+\/%s:\d+->__construct\(',
                            '\{0\} = \(string\(5\)\) "extra"',
                        '\) inside \(object\) class@anonymous\/in\/.+\/%s:\d+',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function () {
                    /** @phpstan-ignore-next-line */
                    return new class ('extra')
                    {
                        private string $message;

                        public function __construct()
                        {
                            $this->message = ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                                $this,
                                new \ReflectionMethod(__CLASS__, __FUNCTION__),
                                func_get_args(),
                            );
                        }

                        public function __toString(): string
                        {
                            return $this->message;
                        }
                    };
                },
            ],
            [
                'An anonymous class, 1 named argument exists, 1 argument is passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in (class@anonymous\/in\/.+\/%s:\d+)',
                        '\-\>__construct\(',
                            '\$a = \(string\(3\)\) "bar"',
                        '\) inside \(object\) \1',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function () {
                    return new class ('bar')
                    {
                        private string $message;

                        public function __construct(string $a)
                        {
                            $this->message = ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                                $this,
                                new \ReflectionMethod(__CLASS__, __FUNCTION__),
                                func_get_args(),
                            );
                        }

                        public function __toString(): string
                        {
                            return $this->message;
                        }
                    };
                },
            ],
            [
                'An anonymous class, 4 named arguments exist, 4 arguments are passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in (class@anonymous\/in\/.+\/%s:\d+)',
                        '\-\>__construct\(',
                            '\$a = \(string\(3\)\) "bar"',
                            ', \$b = \(int\) 42',
                            ', \$c = \(bool\) true',
                            ', \$d = \(float\) 3.14',
                        '\) inside \(object\) \1',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function () {
                    return new class ('bar', 42, true, 3.14)
                    {
                        private string $message;

                        public function __construct(string $a, int $b, bool $c, float $d)
                        {
                            $this->message = ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                                $this,
                                new \ReflectionMethod(__CLASS__, __FUNCTION__),
                                func_get_args(),
                            );
                        }

                        public function __toString(): string
                        {
                            return $this->message;
                        }
                    };
                },
            ],
            [
                'An anonymous class, 4 named arguments exist, 5 arguments are passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in (class@anonymous\/in\/.+\/%s:\d+)',
                        '\-\>__construct\(',
                            '\$a = \(string\(3\)\) "bar"',
                            ', \$b = \(int\) 42',
                            ', \$c = \(bool\) true',
                            ', \$d = \(float\) 3.14',
                            ', \{4\} = \(string\(5\)\) "extra"',
                        '\) inside \(object\) \1',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function () {
                    /** @phpstan-ignore-next-line */
                    return new class ('bar', 42, true, 3.14, 'extra')
                    {
                        private string $message;

                        public function __construct(string $a, int $b, bool $c, float $d)
                        {
                            $this->message = ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                                $this,
                                new \ReflectionMethod(__CLASS__, __FUNCTION__),
                                func_get_args(),
                            );
                        }

                        public function __toString(): string
                        {
                            return $this->message;
                        }
                    };
                },
            ],
            [
                'An anonymous class, 1 named argument exists with an arithmetic default value, no arguments are passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in (class@anonymous.+?\/%s:\d+)',
                        '\-\>__construct\(',
                            '\$a = \(int\) 42',
                        '\) inside \(object\) \1',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function () {
                    return new class
                    {
                        private string $message;

                        public function __construct(int $a = 42)
                        {
                            $this->message = ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                                $this,
                                new \ReflectionMethod(__CLASS__, __FUNCTION__),
                                func_get_args(),
                            );
                        }

                        public function __toString(): string
                        {
                            return $this->message;
                        }
                    };
                },
            ],
            [
                'A class, 1 named argument exists with a constant default value, no arguments are passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in (\\\\%s)\-\>__construct\(',
                            '\$a = \(int\) 99',
                        '\) inside \(object\) \1',
                        '$',
                        '/',
                    ]),
                    preg_quote(testMakeFailureInMethodMessageWorksWithNonStaticMethods_AClassWithADefaultConstant::class, '/'),
                ),
                static function () {
                    return new testMakeFailureInMethodMessageWorksWithNonStaticMethods_AClassWithADefaultConstant();
                },
            ],
            [
                'An anonymous class, 2 named argument, 2nd argument is nullable and null is passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in (class@anonymous.+?\/%s:\d+)',
                        '\-\>__construct\(',
                            '\$a = \(int\) 42',
                            ', \$b = \(null\) null',
                        '\) inside \(object\) \1',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function () {
                    return new class (42, null)
                    {
                        private string $message;

                        public function __construct(int $a, ?string $b)
                        {
                            $this->message = ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                                $this,
                                new \ReflectionMethod(__CLASS__, __FUNCTION__),
                                func_get_args(),
                            );
                        }

                        public function __toString(): string
                        {
                            return $this->message;
                        }
                    };
                },
            ],
            [
                'A named class, 0 named arguments exist, 0 arguments are passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s->__construct\(\)',
                        ' inside \(object\) \\\\%s',
                        '$',
                        '/',
                    ]),
                    preg_quote(testMakeFailureInMethodMessageWorks_ClassANoNamedArguments::class, '/'),
                    preg_quote(testMakeFailureInMethodMessageWorks_ClassANoNamedArguments::class, '/'),
                ),
                static function () {
                    return new testMakeFailureInMethodMessageWorks_ClassANoNamedArguments();
                },
            ],
            [
                'A named class, 0 named arguments exist, 1 argument is passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s->__construct\(',
                            '\{0\} = \(string\(5\)\) "extra"',
                        '\) inside \(object\) \\\\%s',
                        '$',
                        '/',
                    ]),
                    preg_quote(testMakeFailureInMethodMessageWorks_ClassANoNamedArguments::class, '/'),
                    preg_quote(testMakeFailureInMethodMessageWorks_ClassANoNamedArguments::class, '/'),
                ),
                static function () {
                    return new testMakeFailureInMethodMessageWorks_ClassANoNamedArguments('extra');
                },
            ],
            [
                'A named class, 4 named arguments exist, 4 arguments are passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s->__construct\(',
                            '\$a = \(string\(3\)\) "bar"',
                            ', \$b = \(int\) 42',
                            ', \$c = \(bool\) true',
                            ', \$d = \(float\) 3.14',
                        '\)',
                        ' inside \(object\) \\\\%s',
                        '$',
                        '/',
                    ]),
                    preg_quote(testMakeFailureInMethodMessageWorks_ClassB4NamedArguments::class, '/'),
                    preg_quote(testMakeFailureInMethodMessageWorks_ClassB4NamedArguments::class, '/'),
                ),
                static function () {
                    return new testMakeFailureInMethodMessageWorks_ClassB4NamedArguments('bar', 42, true, 3.14);
                },
            ],
            [
                'A named class, 4 named arguments exist, 5 arguments are passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s->__construct\(',
                            '\$a = \(string\(3\)\) "bar"',
                            ', \$b = \(int\) 42',
                            ', \$c = \(bool\) true',
                            ', \$d = \(float\) 3.14',
                            ', \{4\} = \(string\(5\)\) "extra"',
                        '\)',
                        ' inside \(object\) \\\\%s',
                        '$',
                        '/',
                    ]),
                    preg_quote(testMakeFailureInMethodMessageWorks_ClassB4NamedArguments::class, '/'),
                    preg_quote(testMakeFailureInMethodMessageWorks_ClassB4NamedArguments::class, '/'),
                ),
                static function () {
                    return new testMakeFailureInMethodMessageWorks_ClassB4NamedArguments(
                        'bar',
                        42,
                        true,
                        3.14,
                        'extra',
                    );
                },
            ],
            [
                'A named class, where method signature changes between uppermost class and parent classes',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s->__construct\(',
                            '\$a = \(int\) 42',
                            ', \$b = \(bool\) true',
                            ', \$c = \(string\(3\)\) "foo"',
                        '\) inside \(object\) \\\\%s',
                        '$',
                        '/',
                    ]),
                    preg_quote(testMakeFailureInMethodMessageWorks_ANamedClassWhereMethodSignatureChangesBetweenUppermostClassAndParentClasses_C::class, '/'),
                    preg_quote(testMakeFailureInMethodMessageWorks_ANamedClassWhereMethodSignatureChangesBetweenUppermostClassAndParentClasses_A::class, '/'),
                ),
                static function () {
                    return new testMakeFailureInMethodMessageWorks_ANamedClassWhereMethodSignatureChangesBetweenUppermostClassAndParentClasses_A(
                        42,
                    );
                },
            ],
        ];
    }

    /**
     * @dataProvider dataProvider_testMakeFailureInMethodMessageWorksWithStaticMethods
     */
    public function testMakeFailureInMethodMessageWorksWithStaticMethods(
        string $message,
        string $expectedRegex,
        \Closure $callback
    ): void {
        $this->assertMatchesRegularExpression(
            $expectedRegex,
            $callback(),
            $message,
        );
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: object}>
     */
    public function dataProvider_testMakeFailureInMethodMessageWorksWithStaticMethods(): array
    {
        return [
            [
                'An anonymous class, no named arguments exist, no arguments are passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in (class@anonymous\/in\/.+\/%s:\d+)::foo\(\)',
                        ' inside \(class\) \1',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function () {
                    $class = new class
                    {
                        public static function foo(): string
                        {
                            return ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                                get_called_class(),
                                new \ReflectionMethod(get_called_class(), __FUNCTION__),
                                func_get_args(),
                            );
                        }
                    };

                    return $class::foo();
                },
            ],
            [
                'An anonymous class, no named arguments exist, 1 argument is passed',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in (class@anonymous\/in\/.+\/%s:\d+)::foo\(',
                            '\{0\} = \(string\(5\)\) "extra"',
                        '\)',
                        ' inside \(class\) \1',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                static function () {
                    $class = new class
                    {
                        public static function foo(): string
                        {
                            return ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                                get_called_class(),
                                new \ReflectionMethod(get_called_class(), __FUNCTION__),
                                func_get_args(),
                            );
                        }
                    };

                    return $class::foo('extra'); // @phpstan-ignore-line
                },
            ],
        ];
    }

    public function testMakeFailureInMethodMessageWorksWhenArgumentObjectOrClassNameRefersToTheSameClassAsThatForArgumentReflectionMethod(): void
    {
        $object = new class
        {
            public function foo(): string
            {
                return '';
            }
        };

        $reflectionMethod = new \ReflectionMethod($object, 'foo');

        $this->assertMatchesRegularExpression(
            sprintf(
                implode('', [
                    '/',
                    '^',
                    'Failure in (class@anonymous\/in\/.+\/%s:\d+)->foo\(\)',
                    ' inside \(object\) \1',
                    '$',
                    '/',
                ]),
                preg_quote(basename(__FILE__), '/'),
            ),
            ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage($object, $reflectionMethod, []),
        );
    }

    public function testMakeFailureInMethodMessageWorksWhenArgumentObjectOrClassNameIsAChildClassOfTheClassForArgumentReflectionMethod(): void
    {
        $object = new class extends \DateTimeImmutable
        {
        };

        $reflectionMethod = new \ReflectionMethod($object, 'format');

        $this->assertMatchesRegularExpression(
            sprintf(
                implode('', [
                    '/',
                    '^',
                    'Failure in \\\\DateTimeImmutable->format\(',
                        '\$format = \(string\(1\)\) "c"',
                    '\) inside \(object\) class@anonymous\/in\/.+\/%s:\d+ \(',
                        '"\d{4}\-\d{2}\-\d{2}T\d{2}\:\d{2}\:\d{2}[\+\-]\d{2}\:\d{2}"',
                    '\)',
                    '$',
                    '/',
                ]),
                preg_quote(basename(__FILE__), '/'),
                preg_quote(basename(__FILE__), '/'),
            ),
            ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage($object, $reflectionMethod, ['c']),
        );
    }

    public function testMakeFailureInMethodMessageThrowsExceptionWhenClassOfArgumentObjectOrClassNameIsDifferentThanClassFromArgumentReflectionMethod(): void
    {
        $objectA = new class
        {
        };

        $objectB = new class
        {
            public function foo(): string
            {
                return '';
            }
        };

        $reflectionMethod = new \ReflectionMethod($objectB, 'foo');

        try {
            ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage($objectA, $reflectionMethod, []);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>makeFailureInMethodMessage\(',
                            '\$objectOrClassName = \(object\) class@anonymous\/in\/.+\/%s:\d+',
                            ', \$reflectionMethod = \(object\) \\\\ReflectionMethod',
                            ', \$methodArgumentValues = \(array\(0\)\) \[\]',
                        '\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(ExceptionMessageGenerator::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Arguments \$objectOrClassName = \(object\) class@anonymous\/in\/.+\/%s:\d+',
                        ' and \$reflectionMethod = \(object\) \\\\ReflectionMethod \(',
                            'declaring class name: class@anonymous\/in\/.+\/%s:\d+',
                        '\) are problematic as they do not do not reference the same class or a child class hereof',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testMakeFailureInMethodMessageThrowsExceptionWhenArgumentObjectOrClassNameIsInvalid(): void
    {
        $objectB = new class {
            public function foo(): void
            {
            }
        };

        $reflectionMethod = new \ReflectionMethod($objectB, 'foo');

        try {
            ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                42, // @phpstan-ignore-line
                $reflectionMethod,
                [],
            );
        } catch (\Throwable $t) {
            $currentThrowable = $t;
            $this->assertSame(RuntimeException::class, get_class($currentThrowable));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s->makeFailureInMethodMessage\(',
                            '\$objectOrClassName = \(int\) 42',
                            ', \$reflectionMethod = \(object\) \\\\ReflectionMethod',
                            ', \$methodArgumentValues = \(array\(0\)\) \[\]',
                        '\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(ExceptionMessageGenerator::class, '/'),
                ),
                $currentThrowable->getMessage(),
            );

            $currentThrowable = $currentThrowable->getPrevious();
            $this->assertIsObject($currentThrowable);
            assert(is_object($currentThrowable)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentThrowable));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Expects argument \$objectOrClassName to be an object or a string, but it is not\.',
                        ' Found: \(int\) 42',
                        '$',
                        '/',
                    ]),
                    preg_quote(ExceptionMessageGenerator::class, '/'),
                ),
                $currentThrowable->getMessage(),
            );

            $currentThrowable = $currentThrowable->getPrevious();
            $this->assertTrue(null === $currentThrowable);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testMakeFailureInMethodMessageThrowsExceptionWhenArgumentObjectOrClassNameIsAStringAndIsNotASubclassOfTheDeclaringClassOfTheReflectionMethod(): void
    {
        $reflectionMethod = new \ReflectionMethod('DateTime', 'format');

        try {
            ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                'stdClass',
                $reflectionMethod,
                [],
            );
        } catch (\Throwable $t) {
            $currentThrowable = $t;
            $this->assertSame(RuntimeException::class, get_class($currentThrowable));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s->makeFailureInMethodMessage\(',
                            '\$objectOrClassName = \(string\(8\)\) "stdClass"',
                            ', \$reflectionMethod = \(object\) \\\\ReflectionMethod',
                            ', \$methodArgumentValues = \(array\(0\)\) \[\]',
                        '\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(ExceptionMessageGenerator::class, '/'),
                ),
                $currentThrowable->getMessage(),
            );

            $currentThrowable = $currentThrowable->getPrevious();
            $this->assertIsObject($currentThrowable);
            assert(is_object($currentThrowable)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentThrowable));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Arguments \$objectOrClassName \= \(string\(8\)\) "stdClass"',
                        ' and \$reflectionMethod \= \(object\) \\\\ReflectionMethod',
                        ' \(declaring class name: \\\\DateTime\) are problematic as they do not do not reference the',
                        ' same class or a child class hereof',
                        '$',
                        '/',
                    ]),
                    preg_quote(ExceptionMessageGenerator::class, '/'),
                ),
                $currentThrowable->getMessage(),
            );

            $currentThrowable = $currentThrowable->getPrevious();
            $this->assertTrue(null === $currentThrowable);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testMakeFailureInMethodMessageThrowsExceptionWhenArgumentObjectOrClassNameIsAStringAndClassDoesNotExist(): void
    {
        $reflectionMethod = new \ReflectionMethod('DateTime', 'format');

        try {
            ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                'IDoNotExist425393c93a7d435ea6e95b2d0a6ac670', // @phpstan-ignore-line
                $reflectionMethod,
                [],
            );
        } catch (\Throwable $t) {
            $currentThrowable = $t;
            $this->assertSame(RuntimeException::class, get_class($currentThrowable));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s->makeFailureInMethodMessage\(',
                            '\$objectOrClassName = \(string\(43\)\) "IDoNotExist425393c93a7d435ea6e95b2d0a6ac670"',
                            ', \$reflectionMethod = \(object\) \\\\ReflectionMethod',
                            ', \$methodArgumentValues = \(array\(0\)\) \[\]',
                        '\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(ExceptionMessageGenerator::class, '/'),
                ),
                $currentThrowable->getMessage(),
            );

            $currentThrowable = $currentThrowable->getPrevious();
            $this->assertIsObject($currentThrowable);
            assert(is_object($currentThrowable)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentThrowable));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Argument \$objectOrClassName \= \(string\(43\)\)',
                        ' "IDoNotExist425393c93a7d435ea6e95b2d0a6ac670" refers to a non\-existing class',
                        '$',
                        '/',
                    ]),
                    preg_quote(ExceptionMessageGenerator::class, '/'),
                ),
                $currentThrowable->getMessage(),
            );

            $currentThrowable = $currentThrowable->getPrevious();
            $this->assertTrue(null === $currentThrowable);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    /**
     * @dataProvider dataProvider_testMakeUninitializedPropertySafeToTextualIdentifierStringWorks
     */
    public function testMakeUninitializedPropertySafeToTextualIdentifierStringWorks(
        string $message,
        string $expectedRegex,
        object $object
    ): void
    {
        $this->assertMatchesRegularExpression(
            $expectedRegex,
            ExceptionMessageGenerator::getInstance()->makeUninitializedPropertySafeToTextualIdentifierString(
                $object,
                [
                    'a',
                    'b',
                ],
            ),
            $message
        );
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: object}>
     */
    public function dataProvider_testMakeUninitializedPropertySafeToTextualIdentifierStringWorks(): array
    {
        return [
            [
                'An anonymous class without parent',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'class@anonymous.+?\/%s:\d+ \{',
                            '\$a = \(uninitialized\)',
                            ', \$b = \(int\) 42',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                new class
                {
                    private string $a; // @phpstan-ignore-line

                    private int $b = 42; // @phpstan-ignore-line

                    private bool $c; // @phpstan-ignore-line
                },
            ],
            [
                'An anonymous class with a parent and $a property on the parent class',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'class@anonymous.+?\/%s:\d+ \{',
                            '\$a = \(uninitialized\)',
                            ', \$b = \(int\) 42',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                new class extends testMakeUninitializedPropertySafeToTextualIdentifierStringWorks_ClassBParent {
                    private int $b = 42; // @phpstan-ignore-line

                    private bool $c; // @phpstan-ignore-line
                },
            ],
            [
                'A named class without parent',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\\\\%s \{',
                            '\$a = \(uninitialized\)',
                            ', \$b = \(int\) 42',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(testMakeUninitializedPropertySafeToTextualIdentifierStringWorks_ClassA::class, '/'),
                ),
                new testMakeUninitializedPropertySafeToTextualIdentifierStringWorks_ClassA(),
            ],
            [
                'A named class with parent and one property being on the parent',
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        '\\\\%s \{',
                            '\$a = \(uninitialized\)',
                            ', \$b = \(int\) 42',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(testMakeUninitializedPropertySafeToTextualIdentifierStringWorks_ClassB::class, '/'),
                ),
                new testMakeUninitializedPropertySafeToTextualIdentifierStringWorks_ClassB(),
            ],
        ];
    }

    public function testMakeUninitializedPropertySafeToTextualIdentifierStringThrowsExceptionWhenAValueInArgumentPropertyNamesToBeShownIsNotAString(): void
    {
        $object = new class
        {
            protected string $foo;
        };

        try {
            ExceptionMessageGenerator::getInstance()->makeUninitializedPropertySafeToTextualIdentifierString(
                $object,
                [ // @phpstan-ignore-line
                    'foo',
                    42,
                ],
            );
        } catch (\Throwable $t) {
            $currentThrowable = $t;
            $this->assertSame(RuntimeException::class, get_class($currentThrowable));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failure in \\\\%s-\>makeUninitializedPropertySafeToTextualIdentifierString\(',
                            '\$object = \(object\) class@anonymous\/in\/.+\/%s:\d+',
                            ', \$propertyNamesToBeShown = \(array\(2\)\) \[',
                                '\(int\) 0 =\> \(string\(3\)\) "foo"',
                                ', \(int\) 1 =\> \(int\) 42',
                            '\]',
                        '\)',
                        '$',
                        '/',
                    ]),
                    preg_quote(ExceptionMessageGenerator::class, '/'),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentThrowable->getMessage(),
            );

            $currentThrowable = $currentThrowable->getPrevious();
            $this->assertIsObject($currentThrowable);
            assert(is_object($currentThrowable)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentThrowable));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'In argument \$propertyNamesToBeShown, 1\/2 elements are invalid, including\:',
                        ' Element is not a string\: 1 \=\> \(int\) 42',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentThrowable->getMessage(),
            );

            $currentThrowable = $currentThrowable->getPrevious();
            $this->assertTrue(null === $currentThrowable);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    /**
     * @dataProvider dataProvider_testMakeUninitializedPropertySafeToTextualIdentifierStringThrowsExceptionWhenPropertiesDoNotExist
     * @param array<int, string> $propertyNamesToBeShown
     */
    public function testMakeUninitializedPropertySafeToTextualIdentifierStringThrowsExceptionWhenPropertiesDoNotExist(
        string $message,
        \Closure $expectedCallback,
        object $object,
        array $propertyNamesToBeShown
    ): void {
        try {
            ExceptionMessageGenerator::getInstance()->makeUninitializedPropertySafeToTextualIdentifierString(
                $object,
                $propertyNamesToBeShown,
            );
        } catch (\Throwable $t) {
            $expectedCallback($message, $t);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    /**
     * @return array<int, array{0: string, 1: \Closure, 2: object, 3: array<int, string>}>
     */
    public function dataProvider_testMakeUninitializedPropertySafeToTextualIdentifierStringThrowsExceptionWhenPropertiesDoNotExist(): array
    {
        return [
            [
                'An anonymous class without parent',
                function (string $message, \Throwable $t): void {
                    $currentThrowable = $t;
                    $this->assertSame(RuntimeException::class, get_class($currentThrowable));
                    $this->assertMatchesRegularExpression(
                        sprintf(
                            implode('', [
                                '/',
                                '^',
                                'Failure in \\\\%s-\>makeUninitializedPropertySafeToTextualIdentifierString\(',
                                    '\$object = \(object\) class@anonymous.+?\/%s:\d+',
                                    ', \$propertyNamesToBeShown = \(array\(1\)\) \[\(int\) 0 =\> \(string\(1\)\) "b"\]',
                                '\)',
                                '$',
                                '/',
                            ]),
                            preg_quote(ExceptionMessageGenerator::class, '/'),
                            preg_quote(basename(__FILE__), '/'),
                        ),
                        $currentThrowable->getMessage(),
                        $message,
                    );

                    $currentThrowable = $currentThrowable->getPrevious();
                    $this->assertIsObject($currentThrowable);
                    assert(is_object($currentThrowable)); // Make phpstan happy
                    $this->assertSame(RuntimeException::class, get_class($currentThrowable));
                    $this->assertMatchesRegularExpression(
                        sprintf(
                            implode('', [
                                '/',
                                '^',
                                'In argument \$propertyNamesToBeShown, 1\/1 elements are invalid, including:',
                                    ' Property "b" does not exist on class',
                                        ' class@anonymous.+?\/%s:\d+',
                                '$',
                                '/',
                            ]),
                            preg_quote(basename(__FILE__), '/'),
                        ),
                        $currentThrowable->getMessage(),
                        $message,
                    );

                    $currentThrowable = $currentThrowable->getPrevious();
                    $this->assertTrue(null === $currentThrowable);
                },
                new class
                {
                    protected int $a = 1;
                },
                ['b'],
            ],
            [
                'An anonymous class with a parent and the $a property on the parent',
                function (string $message, \Throwable $t): void {
                    $currentThrowable = $t;
                    $this->assertSame(RuntimeException::class, get_class($currentThrowable));
                    $this->assertMatchesRegularExpression(
                        sprintf(
                            implode('', [
                                '/',
                                '^',
                                'Failure in \\\\%s-\>makeUninitializedPropertySafeToTextualIdentifierString\(',
                                    '\$object = \(object\) class@anonymous.+?\/%s:\d+',
                                    ', \$propertyNamesToBeShown = \(array\(1\)\) \[\(int\) 0 =\> \(string\(1\)\) "b"\]',
                                '\)',
                                '$',
                                '/',
                            ]),
                            preg_quote(ExceptionMessageGenerator::class, '/'),
                            preg_quote(basename(__FILE__), '/'),
                        ),
                        $currentThrowable->getMessage(),
                        $message,
                    );

                    $currentThrowable = $currentThrowable->getPrevious();
                    $this->assertIsObject($currentThrowable);
                    assert(is_object($currentThrowable)); // Make phpstan happy
                    $this->assertSame(RuntimeException::class, get_class($currentThrowable));
                    $this->assertMatchesRegularExpression(
                        sprintf(
                            implode('', [
                                '/',
                                '^',
                                'In argument \$propertyNamesToBeShown, 1\/1 elements are invalid, including:',
                                    ' Property "b" does not exist on class',
                                        ' class@anonymous.+?\/%s:\d+',
                                        ' or any of its parent classes',
                                '$',
                                '/',
                            ]),
                            preg_quote(basename(__FILE__), '/'),
                        ),
                        $currentThrowable->getMessage(),
                        $message,
                    );

                    $currentThrowable = $currentThrowable->getPrevious();
                    $this->assertTrue(null === $currentThrowable);
                },
                new class extends testMakeUninitializedPropertySafeToTextualIdentifierStringThrowsExceptionWhenPropertiesDoNotExist_ClassBParent
                {
                    protected int $a = 1;
                },
                ['b'],
            ],
            [
                'An named class without parent',
                function (string $message, \Throwable $t): void {
                    $currentThrowable = $t;
                    $this->assertSame(RuntimeException::class, get_class($currentThrowable));
                    $this->assertMatchesRegularExpression(
                        sprintf(
                            implode('', [
                                '/',
                                '^',
                                'Failure in \\\\%s-\>makeUninitializedPropertySafeToTextualIdentifierString\(',
                                    '\$object = \(object\) \\\\%s',
                                    ', \$propertyNamesToBeShown = \(array\(1\)\) \[\(int\) 0 =\> \(string\(1\)\) "b"\]',
                                '\)',
                                '$',
                                '/',
                            ]),
                            preg_quote(ExceptionMessageGenerator::class, '/'),
                            preg_quote(testMakeUninitializedPropertySafeToTextualIdentifierStringThrowsExceptionWhenPropertiesDoNotExist_ClassA::class, '/'),
                        ),
                        $currentThrowable->getMessage(),
                        $message,
                    );

                    $currentThrowable = $currentThrowable->getPrevious();
                    $this->assertIsObject($currentThrowable);
                    assert(is_object($currentThrowable)); // Make phpstan happy
                    $this->assertSame(RuntimeException::class, get_class($currentThrowable));
                    $this->assertMatchesRegularExpression(
                        sprintf(
                            implode('', [
                                '/',
                                '^',
                                'In argument \$propertyNamesToBeShown, 1\/1 elements are invalid, including:',
                                    ' Property "b" does not exist on class \\\\%s',
                                '$',
                                '/',
                            ]),
                            preg_quote(testMakeUninitializedPropertySafeToTextualIdentifierStringThrowsExceptionWhenPropertiesDoNotExist_ClassA::class, '/'),
                        ),
                        $currentThrowable->getMessage(),
                        $message,
                    );

                    $currentThrowable = $currentThrowable->getPrevious();
                    $this->assertTrue(null === $currentThrowable);
                },
                new testMakeUninitializedPropertySafeToTextualIdentifierStringThrowsExceptionWhenPropertiesDoNotExist_ClassA(),
                ['b'],
            ],
            [
                'An named class with a parent and $a property on parent class',
                function (string $message, \Throwable $t): void {
                    $currentThrowable = $t;
                    $this->assertSame(RuntimeException::class, get_class($currentThrowable));
                    $this->assertMatchesRegularExpression(
                        sprintf(
                            implode('', [
                                '/',
                                '^',
                                'Failure in \\\\%s-\>makeUninitializedPropertySafeToTextualIdentifierString\(',
                                    '\$object = \(object\) \\\\%s',
                                    ', \$propertyNamesToBeShown = \(array\(1\)\) \[\(int\) 0 =\> \(string\(1\)\) "b"\]',
                                '\)',
                                '$',
                                '/',
                            ]),
                            preg_quote(ExceptionMessageGenerator::class, '/'),
                            preg_quote(testMakeUninitializedPropertySafeToTextualIdentifierStringThrowsExceptionWhenPropertiesDoNotExist_ClassB::class, '/'),
                        ),
                        $currentThrowable->getMessage(),
                        $message,
                    );

                    $currentThrowable = $currentThrowable->getPrevious();
                    $this->assertIsObject($currentThrowable);
                    assert(is_object($currentThrowable)); // Make phpstan happy
                    $this->assertSame(RuntimeException::class, get_class($currentThrowable));
                    $this->assertMatchesRegularExpression(
                        sprintf(
                            implode('', [
                                '/',
                                '^',
                                'In argument \$propertyNamesToBeShown, 1\/1 elements are invalid, including:',
                                ' Property "b" does not exist on class \\\\%s',
                                ' or any of its parent classes',
                                '$',
                                '/',
                            ]),
                            preg_quote(testMakeUninitializedPropertySafeToTextualIdentifierStringThrowsExceptionWhenPropertiesDoNotExist_ClassB::class, '/'),
                        ),
                        $currentThrowable->getMessage(),
                        $message,
                    );

                    $currentThrowable = $currentThrowable->getPrevious();
                    $this->assertTrue(null === $currentThrowable);
                },
                new testMakeUninitializedPropertySafeToTextualIdentifierStringThrowsExceptionWhenPropertiesDoNotExist_ClassB(),
                ['b'],
            ],
        ];
    }

    public function testWithCasterWorks(): void
    {
        $casterA = $this->mockCasterInterface();
        $exceptionMessageGeneratorA = new ExceptionMessageGenerator($casterA);

        $casterB = $this->mockCasterInterface();
        $exceptionMessageGeneratorB = $exceptionMessageGeneratorA->withCaster($casterB);

        $this->assertNotSame($exceptionMessageGeneratorA, $exceptionMessageGeneratorB);
        $this->assertSame($casterA, $exceptionMessageGeneratorA->getCaster());
        $this->assertSame($casterB, $exceptionMessageGeneratorB->getCaster());
    }

    /**
     * @runTestInSeparateProcess
     */
    public function testGetInstanceWorks(): void
    {
        $exceptionMessageGeneratorA = ExceptionMessageGenerator::getInstance();

        $this->assertSame(ExceptionMessageGenerator::class, get_class($exceptionMessageGeneratorA));

        $exceptionMessageGeneratorB = ExceptionMessageGenerator::getInstance();

        $this->assertSame($exceptionMessageGeneratorA, $exceptionMessageGeneratorB);
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
