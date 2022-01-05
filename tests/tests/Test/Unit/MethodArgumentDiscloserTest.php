<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional;

use Eboreum\Exceptional\AbstractFunctionArgumentDiscloser;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\MethodArgumentDiscloser;
use PHPUnit\Framework\TestCase;

define(
    'EBOREUM_EXCEPTIONAL_TEST_323586a4460042c286a544d258337226',
    sprintf(
        'A global constant utilized in unit test %s\\MethodArgumentDiscloserTest->testBasics',
        __NAMESPACE__,
    ),
);

define(
    __NAMESPACE__ . '\\EBOREUM_EXCEPTIONAL_TEST_2098a8136eb848ce8d23f0e42a5d8a7a',
    sprintf(
        'A namespaced constant utilized in unit test %s\\MethodArgumentDiscloserTest->testBasics',
        __NAMESPACE__,
    ),
);

class MethodArgumentDiscloserTest extends TestCase
{
    /**
     * @dataProvider dataProvider_testBasics
     */
    public function testBasics(
        string $message,
        \Closure $objectFactory,
        \Closure $objectValueFactoryCallback,
        \Closure $assertionsCallback
    ): void {
        $object = $objectFactory();

        $this->assertTrue(is_object($object));

        [
            $reflectionMethod,
            $methodArgumentValues,
            $methodArgumentDiscloser
        ] = $objectValueFactoryCallback($object);

        $this->assertSame($reflectionMethod, $methodArgumentDiscloser->getReflectionFunction(), $message);
        $this->assertSame($methodArgumentValues, $methodArgumentDiscloser->getFunctionArgumentValues(), $message);
        $this->assertSame(
            count($methodArgumentValues),
            $methodArgumentDiscloser->getFunctionArgumentValuesCount(),
            $message,
        );

        $assertionsCallback($message, $methodArgumentDiscloser, $object);
    }

    /**
     * @return array<array{string, \Closure(): object, \Closure, \Closure(string, MethodArgumentDiscloser, object): void}>
     */
    public function dataProvider_testBasics(): array
    {
        return [
            [
                '0 named parameters. 0 passed argument values.',
                static function (): object {
                    return new class
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo();
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(-1, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(0, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        0,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(0, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        0,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '1 named parameter. $a is optional with default value 42. 0 passed argument values.',
                static function (): object {
                    return new class
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int $a = 42): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo();
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(0, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [42],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        1,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        0,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '1 named parameter. $a is optional with default value 42. 1 passed argument value.',
                static function (): object {
                    /**
                     * phpstan seems buggy, because if we just do "return new class" here, it get confused and mixes up
                     * the lines, taking the closure from a completely different test case.
                     */
                    return new class
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int $a = 42): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(64);
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(0, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [64],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        1,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        0,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. All required. 3 passed argument values.',
                static function (): object {
                    return new class
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int $a, string $b, float $c): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(42, 'bar', 3.14);
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            3.14,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(0, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. All required. 4 passed argument values.',
                static function (): object {
                    $object = new class
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int $a, string $b, float $c): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };

                    return $object; // Needs to be in a variable because phpstan gets confused
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(42, 'bar', 3.14, true);
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            3.14,
                            true,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        4,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(0, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. $c is optional with default value being null. 2 passed argument values.',
                static function (): object {
                    return new class
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int $a, string $b, ?float $c = null): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(42, 'bar');
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            null,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a global constant',
                    ', EBOREUM_EXCEPTIONAL_TEST_323586a4460042c286a544d258337226. 2 passed argument values.',
                ]),
                static function (): object {
                    return new class
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(
                            int $a,
                            string $b,
                            string $c = \EBOREUM_EXCEPTIONAL_TEST_323586a4460042c286a544d258337226
                        ): array {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(42, 'bar');
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            \EBOREUM_EXCEPTIONAL_TEST_323586a4460042c286a544d258337226,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a global constant',
                    ', EBOREUM_EXCEPTIONAL_TEST_323586a4460042c286a544d258337226. 3 passed argument values.',
                ]),
                static function (): object {
                    return new class
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(
                            int $a,
                            string $b,
                            string $c = \EBOREUM_EXCEPTIONAL_TEST_323586a4460042c286a544d258337226
                        ): array {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(42, 'bar', 'baz');
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            'baz',
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a namespaced constant',
                    ', EBOREUM_EXCEPTIONAL_TEST_2098a8136eb848ce8d23f0e42a5d8a7a. 2 passed argument values.',
                ]),
                static function (): object {
                    return new class
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(
                            int $a,
                            string $b,
                            string $c = EBOREUM_EXCEPTIONAL_TEST_2098a8136eb848ce8d23f0e42a5d8a7a
                        ): array {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(42, 'bar');
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            EBOREUM_EXCEPTIONAL_TEST_2098a8136eb848ce8d23f0e42a5d8a7a,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a namespaced constant',
                    ', EBOREUM_EXCEPTIONAL_TEST_2098a8136eb848ce8d23f0e42a5d8a7a. 3 passed argument values.',
                ]),
                static function (): object {
                    return new class
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(
                            int $a,
                            string $b,
                            string $c = EBOREUM_EXCEPTIONAL_TEST_2098a8136eb848ce8d23f0e42a5d8a7a
                        ): array {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(42, 'bar', 'baz');
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            'baz',
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a same-class constant, self::BAR.',
                    ' being public. 2 passed argument values.',
                ]),
                static function (): object {
                    return new class
                    {
                        public const BAR = 3.14;

                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int $a, string $b, ?float $c = self::BAR): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(42, 'bar');
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            3.14,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a same-class constant, self::BAR.',
                    ' being private. 2 passed argument values.',
                ]),
                static function (): object {
                    return new class
                    {
                        private const BAR = 3.14;

                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int $a, string $b, ?float $c = self::BAR): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(42, 'bar');
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            3.14,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a same-class constant, self::BAR.',
                    ' 3 passed argument values, overriding $c.',
                ]),
                static function (): object {
                    return new class
                    {
                        public const BAR = 3.14;

                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int $a, string $b, ?float $c = self::BAR): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(42, 'bar', 2.72);
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            2.72,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a parent class constant',
                    ', using parent binding, parent::ATOM. 2 passed argument values.',
                ]),
                static function (): object {
                    return new class extends \DateTimeImmutable
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int $a, string $b, string $c = parent::ATOM): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(42, 'bar');
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            \DateTimeInterface::ATOM,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a parent class constant',
                    ', using parent binding, parent::ATOM. 3 passed argument values.',
                ]),
                static function (): object {
                    return new class extends \DateTimeImmutable
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int $a, string $b, string $c = parent::ATOM): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(42, 'bar', 'baz');
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            'baz',
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a parent class constant',
                    ', using parent binding, \DateTimeInterface::ATOM. 2 passed argument values.',
                ]),
                static function (): object {
                    return new class extends \DateTimeImmutable
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int $a, string $b, string $c = \DateTimeInterface::ATOM): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(42, 'bar');
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            \DateTimeInterface::ATOM,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a parent class constant',
                    ', using parent binding, \DateTimeImmutable::ATOM. 2 passed argument values.',
                    ' Notice: \DateTimeImmutable - not \DateTimeInterface - is used here.',
                ]),
                static function (): object {
                    return new class extends \DateTimeImmutable
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int $a, string $b, string $c = \DateTimeImmutable::ATOM): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(42, 'bar');
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            \DateTimeInterface::ATOM,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a parent class constant',
                    ', using parent binding, \DateTimeInterface::ATOM. 3 passed argument values.',
                ]),
                static function (): object {
                    return new class
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int $a, string $b, string $c = \DateTimeInterface::ATOM): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(42, 'bar', 'baz');
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            'baz',
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '1 named parameters. $a is variadic. 0 passed argument values.',
                static function (): object {
                    return new class
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int ...$a): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo();
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(0, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            [],
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        1,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        0,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(true, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '1 named parameters. $a is variadic. 1 passed argument values.',
                static function (): object {
                    $object = new class
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int ...$a): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };

                    return $object; // Needs to be in a variable because phpstan gets confused
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(...[1, 2, 3]);
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(0, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            [1,2,3],
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        1,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        0,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(true, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. $c is variadic. 0 passed argument values.',
                static function (): object {
                    return new class
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int $a = 42, string $b = 'baz', float ...$c): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo();
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'baz',
                            [],
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        0,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(true, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. $c is variadic. 2 passed argument values.',
                static function (): object {
                    $object = new class
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int $a = 42, string $b = 'baz', float ...$c): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };

                    return $object; // Needs to be in a variable because phpstan gets confused
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(43, 'bim');
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            43,
                            'bim',
                            [],
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        0,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(true, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. $c is variadic. 3 passed argument values.',
                static function (): object {
                    $object = new class
                    {
                        /**
                         * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                         */
                        public function foo(int $a = 42, string $b = 'baz', float ...$c): array
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
                            $methodArgumentValues = func_get_args();

                            return [
                                $reflectionMethod,
                                $methodArgumentValues,
                                new MethodArgumentDiscloser(
                                    Caster::getInstance(),
                                    $reflectionMethod,
                                    $methodArgumentValues
                                ),
                            ];
                        }
                    };

                    assert(is_object($object)); // Needs to here because otherwise, phpstan gets confused

                    return $object; // Needs to be in a variable because phpstan gets confused
                },
                /**
                 * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
                 */
                static function (object $object): array {
                    assert(method_exists($object, 'foo'));

                    return $object->foo(43, 'bim', ...[1.0, 2.0, 3.0]);
                },
                function (
                    string $message,
                    MethodArgumentDiscloser $methodArgumentDiscloser,
                    object $object
                ): void {
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            43,
                            'bim',
                            [1.0,2.0,3.0],
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(0)); // Make phpstan happy
                    $this->assertSame(
                        'a',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(1)); // Make phpstan happy
                    $this->assertSame(
                        'b',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    assert(null !== $methodArgumentDiscloser->getReflectionParameterByIndex(2)); // Make phpstan happy
                    $this->assertSame(
                        'c',
                        $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        0,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(true, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
        ];
    }

    /**
     * @dataProvider dataProvider_testConstructorThrowsExceptionWhenArgumentMethodArgumentValuesContainsFewerElementsThanTheNumberOfRequiredParametersInArgumentReflectionMethod
     */
    public function testConstructorThrowsExceptionWhenArgumentMethodArgumentValuesContainsFewerElementsThanTheNumberOfRequiredParametersInArgumentReflectionMethod(
        string $message,
        string $glue,
        \ReflectionMethod $reflectionMethod
    ): void {
        try {
            new MethodArgumentDiscloser(Caster::getInstance(), $reflectionMethod, []);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException), $message);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failed to construct \\\\%s with arguments \{',
                            '\$caster = \(object\) \\\\%s',
                            ', \$reflectionMethod = \(object\) \\\\ReflectionMethod',
                            ', \$methodArgumentValues = \(array\(0\)\) \[\]',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(MethodArgumentDiscloser::class, '/'),
                    preg_quote(Caster::class, '/'),
                ),
                $currentException->getMessage(),
                $message,
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException, $message);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException), $message);
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Argument \$methodArgumentValues = \(array\(0\)\) \[\] contains fewer elements \(0\)',
                        ' than the required number of parameters \(1\) in argument',
                        ' \$reflectionMethod = \(object\) \\\\ReflectionMethod \(',
                            'class@anonymous\/in\/.+\/%s:\d+%sfoo',
                        '\), which is bogus',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                    preg_quote($glue, '/'),
                ),
                $currentException->getMessage(),
                $message,
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException, $message);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: object}>
     */
    public function dataProvider_testConstructorThrowsExceptionWhenArgumentMethodArgumentValuesContainsFewerElementsThanTheNumberOfRequiredParametersInArgumentReflectionMethod(): array
    {
        return [
            [
                'Non-static',
                '->',
                (static function () {
                    $object = new class
                    {
                        public function foo(int $a): \ReflectionMethod
                        {
                            return new \ReflectionMethod($this, __FUNCTION__);
                        }
                    };

                    return $object->foo(42);
                })(),
            ],
            [
                'Static',
                '::',
                (static function () {
                    $object = new class
                    {
                        public static function foo(int $a): \ReflectionMethod
                        {
                            return new \ReflectionMethod(self::class, __FUNCTION__);
                        }
                    };

                    return $object::foo(42);
                })(),
            ],
        ];
    }

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenNoDefaultValueIsAvailableOnReflectionParameter(): void
    {
        $object = new class
        {
            public function foo(int $a): MethodArgumentDiscloser
            {
                $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);

                return new MethodArgumentDiscloser(Caster::getInstance(), $reflectionMethod, [42]);
            }
        };

        $methodArgumentDiscloser = $object->foo(42);

        try {
            $methodArgumentDiscloser->getDefaultValueForReflectionParameter(
                $methodArgumentDiscloser->getReflectionFunction()->getParameters()[0]
            );
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Expects argument \$reflectionParameter \(name: "a"\) to have a default value available',
                        ', but it does not',
                        '$',
                        '/',
                    ]),
                    preg_quote(MethodArgumentDiscloser::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenClassConstantNamePointsToANonExistingClassConstant(): void
    {
        $object = new class
        {
            public function foo(
                /** @phpstan-ignore-next-line */
                int $a = self::BAR
            ): void {
            }
        };

        $reflectionObject = new \ReflectionObject($object);
        $reflectionMethod = $reflectionObject->getMethod('foo');
        $methodArgumentDiscloser = new MethodArgumentDiscloser(Caster::getInstance(), $reflectionMethod, []);
        $reflectionParameter = $reflectionMethod->getParameters()[0];

        try {
            $methodArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Parameter \$a in method class@anonymous\/in\/.+\/%s:\d+->foo',
                        ' has a default value, which is a constant, but a problem with this constant was encountered',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Unable to locate the constant self\:\:BAR',
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

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenClassConstantNamePointsToAConstantOnANonExistingParentClass(): void
    {
        $object = new class
        {
            public function foo(
                /** @phpstan-ignore-next-line */
                int $a = parent::BAR
            ): void {
            }
        };

        $reflectionObject = new \ReflectionObject($object);
        $reflectionMethod = $reflectionObject->getMethod('foo');
        $methodArgumentDiscloser = new MethodArgumentDiscloser(Caster::getInstance(), $reflectionMethod, []);
        $reflectionParameter = $reflectionMethod->getParameters()[0];

        try {
            $methodArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Parameter \$a in method class@anonymous\/in\/.+\/%s:\d+->foo',
                        ' has a default value, which is a constant, but a problem with this constant was encountered',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Unable to locate the constant parent\:\:BAR',
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

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenClassConstantNamePointsToAConstantWhichDoesNotExistOnTheParentClass(): void
    {
        $object = new class extends \DateTimeImmutable
        {
            public function foo(
                /** @phpstan-ignore-next-line */
                int $a = parent::I_DONT_EXIST_836a6cf1a90749d0831ebcb8cb7776a4
            ): void {
            }
        };

        $reflectionObject = new \ReflectionObject($object);
        $reflectionMethod = $reflectionObject->getMethod('foo');
        $methodArgumentDiscloser = new MethodArgumentDiscloser(Caster::getInstance(), $reflectionMethod, []);
        $reflectionParameter = $reflectionMethod->getParameters()[0];

        try {
            $methodArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Parameter \$a in method class@anonymous\/in\/.+\/%s:\d+->foo',
                        ' has a default value, which is a constant, but a problem with this constant was encountered',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Unable to locate the constant parent\:\:I_DONT_EXIST_836a6cf1a90749d0831ebcb8cb7776a4',
                    ' or at any parent class',
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

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenCaseForScopeIsUncovered(): void
    {
        $object = new class
        {
            public function foo(
                /** @phpstan-ignore-next-line */
                int $a = \IDontExista8728361d30f42bfb9a954abfac4ccab::BAR
            ): void {
            }
        };

        $reflectionMethod = new \ReflectionMethod($object, 'foo');

        $discloser = new class (Caster::getInstance(), $reflectionMethod) extends AbstractFunctionArgumentDiscloser
        {
            public function __construct(Caster $caster, \ReflectionMethod $reflectionMethod)
            {
                $this->caster = $caster;
                $this->reflectionFunction = $reflectionMethod;
            }

            /**
             * {@inheritDoc}
             */
            public static function getDefaultValueConstantRegex(): string
            {
                return '/^(?<scope>(IDontExista8728361d30f42bfb9a954abfac4ccab))::(?<scopedName>(\w+))$/';
            }
        };

        $reflectionParameter = $reflectionMethod->getParameters()[0];

        try {
            $discloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Parameter \$a in method class@anonymous\/in\/.+\/%s:\d+->foo',
                        ' has a default value, which is a constant, but a problem with this constant was encountered',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Uncovered case for \$match\[\'scope\'\] \=',
                    ' \(string\(42\)\) "IDontExista8728361d30f42bfb9a954abfac4ccab"',
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

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenClassConstantNamePointsToANonExistingFullyQuantifiedClassConstant(): void
    {
        $object = new class
        {
            public function foo(
                /** @phpstan-ignore-next-line */
                int $a = \IDontExist2da718442a7547e2b970aed55a2324b0::BAR
            ): void
            {
            }
        };

        $reflectionObject = new \ReflectionObject($object);
        $reflectionMethod = $reflectionObject->getMethod('foo');
        $methodArgumentDiscloser = new MethodArgumentDiscloser(Caster::getInstance(), $reflectionMethod, []);
        $reflectionParameter = $reflectionMethod->getParameters()[0];

        try {
            $methodArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Parameter \$a in method class@anonymous\/in\/.+\/%s:\d+->foo',
                        ' has a default value, which is a constant, but a problem with this constant was encountered',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Class constant "IDontExist2da718442a7547e2b970aed55a2324b0\:\:BAR" is not defined',
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

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenConstantNameDoesNotMatchRegularExpressionForNonStaticMethod(): void
    {
        $object = new class
        {
            public function foo(int $a): MethodArgumentDiscloser
            {
                $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);

                return new MethodArgumentDiscloser(Caster::getInstance(), $reflectionMethod, [42]);
            }
        };

        $methodArgumentDiscloser = $object->foo(42);

        $reflectionParameter = $this
            ->getMockBuilder('ReflectionParameter')
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method('isDefaultValueAvailable')
            ->with()
            ->willReturn(true);

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method('isDefaultValueConstant')
            ->with()
            ->willReturn(true);

        $reflectionParameter
            ->expects($this->exactly(3))
            ->method('getDefaultValueConstantName')
            ->with()
            ->willReturn('  I don\'t work as a constant name  ');

        $reflectionParameter
            ->expects($this->exactly(2))
            ->method('getName')
            ->with()
            ->willReturn('foo');

        $reflectionParameter
            ->expects($this->exactly(2))
            ->method('getDeclaringClass')
            ->with()
            ->willReturn($methodArgumentDiscloser->getReflectionFunction()->getDeclaringClass());

        $reflectionParameter
            ->expects($this->exactly(4))
            ->method('getDeclaringFunction')
            ->with()
            ->willReturn($methodArgumentDiscloser->getReflectionFunction());

        try {
            $methodArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Parameter \$foo in method class@anonymous\/in\/.+\/%s:\d+->foo',
                        ' has a default value, which is a constant, but a problem with this constant was encountered',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Expects default value of parameter \$foo - a constant - to match regular expression \'.+\'',
                    ', but it does not\. Found: \(string\(35\)\) "  I don\'t work as a constant name  "',
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

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenConstantNameDoesNotMatchRegularExpressionForStaticMethod(): void
    {
        $object = new class
        {
            public static function foo(int $a): MethodArgumentDiscloser
            {
                $reflectionMethod = new \ReflectionMethod(self::class, __FUNCTION__);

                return new MethodArgumentDiscloser(Caster::getInstance(), $reflectionMethod, [42]);
            }
        };

        $methodArgumentDiscloser = $object->foo(42);

        $reflectionParameter = $this
            ->getMockBuilder('ReflectionParameter')
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method('isDefaultValueAvailable')
            ->with()
            ->willReturn(true);

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method('isDefaultValueConstant')
            ->with()
            ->willReturn(true);

        $reflectionParameter
            ->expects($this->exactly(3))
            ->method('getDefaultValueConstantName')
            ->with()
            ->willReturn('  I don\'t work as a constant name  ');

        $reflectionParameter
            ->expects($this->exactly(2))
            ->method('getName')
            ->with()
            ->willReturn('foo');

        $reflectionParameter
            ->expects($this->exactly(2))
            ->method('getDeclaringClass')
            ->with()
            ->willReturn($methodArgumentDiscloser->getReflectionFunction()->getDeclaringClass());

        $reflectionParameter
            ->expects($this->exactly(4))
            ->method('getDeclaringFunction')
            ->with()
            ->willReturn($methodArgumentDiscloser->getReflectionFunction());

        try {
            $methodArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Parameter \$foo in method class@anonymous\/in\/.+\/%s:\d+\:\:foo',
                        ' has a default value, which is a constant, but a problem with this constant was encountered',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Expects default value of parameter \$foo - a constant - to match regular expression \'.+\'',
                    ', but it does not\. Found: \(string\(35\)\) "  I don\'t work as a constant name  "',
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

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedGlobalConstantDoesNotExist(): void
    {
        $object = new class
        {
            public function foo(int $a): MethodArgumentDiscloser
            {
                $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);

                return new MethodArgumentDiscloser(Caster::getInstance(), $reflectionMethod, [42]);
            }
        };

        $methodArgumentDiscloser = $object->foo(42);

        $reflectionParameter = $this
            ->getMockBuilder('ReflectionParameter')
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method('isDefaultValueAvailable')
            ->with()
            ->willReturn(true);

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method('isDefaultValueConstant')
            ->with()
            ->willReturn(true);

        $reflectionParameter
            ->expects($this->exactly(2))
            ->method('getDefaultValueConstantName')
            ->with()
            ->willReturn('NONEXSITING_CONSTANT_1aedab95b22c45afbdd0e5cf93af5ee9');

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method('getName')
            ->with()
            ->willReturn('foo');

        $reflectionParameter
            ->expects($this->exactly(2))
            ->method('getDeclaringClass')
            ->with()
            ->willReturn($methodArgumentDiscloser->getReflectionFunction()->getDeclaringClass());

        $reflectionParameter
            ->expects($this->exactly(4))
            ->method('getDeclaringFunction')
            ->with()
            ->willReturn($methodArgumentDiscloser->getReflectionFunction());

        try {
            $methodArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Parameter \$foo in method class@anonymous\/in\/.+\/%s:\d+->foo',
                        ' has a default value, which is a constant, but a problem with this constant was encountered',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'The global constant "NONEXSITING_CONSTANT_1aedab95b22c45afbdd0e5cf93af5ee9" is not defined',
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

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist(): void
    {
        $object = new class
        {
            public function foo(int $a): MethodArgumentDiscloser
            {
                $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);

                return new MethodArgumentDiscloser(Caster::getInstance(), $reflectionMethod, [42]);
            }
        };

        $methodArgumentDiscloser = $object->foo(42);

        $reflectionParameter = $this
            ->getMockBuilder('ReflectionParameter')
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method('isDefaultValueAvailable')
            ->with()
            ->willReturn(true);

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method('isDefaultValueConstant')
            ->with()
            ->willReturn(true);

        $reflectionParameter
            ->expects($this->exactly(2))
            ->method('getDefaultValueConstantName')
            ->with()
            ->willReturn('Foo\\Bar\\NONEXSITING_CONSTANT_e68ff2bd2d214c59abb3ad374163871f');

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method('getName')
            ->with()
            ->willReturn('foo');

        $reflectionParameter
            ->expects($this->exactly(2))
            ->method('getDeclaringClass')
            ->with()
            ->willReturn($methodArgumentDiscloser->getReflectionFunction()->getDeclaringClass());

        $reflectionParameter
            ->expects($this->exactly(4))
            ->method('getDeclaringFunction')
            ->with()
            ->willReturn($methodArgumentDiscloser->getReflectionFunction());

        try {
            $methodArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Parameter \$foo in method class@anonymous\/in\/.+\/%s:\d+->foo',
                        ' has a default value, which is a constant, but a problem with this constant was encountered',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'The namespaced constant',
                    ' "Foo\\\\\\\\Bar\\\\\\\\NONEXSITING_CONSTANT_e68ff2bd2d214c59abb3ad374163871f"',
                    ' is not defined',
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

    public function testGetDefaultValueForReflectionParameterHandlesUncoveredCaseGracefully(): void
    {
        $object = new class
        {
            public const BAR = 42;

            public function foo(int $a = self::BAR): void
            {
            }
        };

        $reflectionMethod = new \ReflectionMethod($object, 'foo');

        $discloser = new class (Caster::getInstance(), $reflectionMethod) extends AbstractFunctionArgumentDiscloser
        {
            public function __construct(Caster $caster, \ReflectionMethod $reflectionMethod)
            {
                $this->caster = $caster;
                $this->reflectionFunction = $reflectionMethod;
            }

            /**
             * {@inheritDoc}
             */
            public static function getDefaultValueConstantRegex(): string
            {
                return '/^.+$/';
            }
        };

        $reflectionParameter = $reflectionMethod->getParameters()[0];

        try {
            $discloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Parameter \$a in method class@anonymous\/in\/.+\/%s:\d+->foo',
                        ' has a default value, which is a constant, but a problem with this constant was encountered',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            assert(is_object($currentException)); // Make phpstan happy
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Uncovered case for constant name "self\:\:BAR"',
                    ' and \$match \= \(array\(1\)\) \[\(int\) 0 \=\> \(string\(9\)\) "self\:\:BAR"\]',
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
}
