<?php

declare(strict_types = 1);

namespace Test\Unit\Eboreum\Exceptional;

use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\MethodArgumentDiscloser;
use PHPUnit\Framework\TestCase;

define(
    "EBOREUM_EXCEPTIONAL_TEST_323586a4460042c286a544d258337226",
    sprintf(
        "A global constant utilized in unit test %s\\MethodArgumentDiscloserTest->testBasics",
        __NAMESPACE__,
    ),
);

define(
    __NAMESPACE__ . "\\EBOREUM_EXCEPTIONAL_TEST_2098a8136eb848ce8d23f0e42a5d8a7a",
    sprintf(
        "A namespaced constant utilized in unit test %s\\MethodArgumentDiscloserTest->testBasics",
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
    ): void
    {
        $object = $objectFactory();

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
     * @return array<array{0: string, 1: \Closure, 2: \Closure, 3: \Closure}>
     */
    public function dataProvider_testBasics(): array
    {
        return [
            [
                "0 named parameters. 0 passed argument values.",
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
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
                function(object $object){
                    return $object->foo(); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(-1, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(0, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(0, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
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
                "1 named parameter. \$a is optional with default value 42. 0 passed argument values.",
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
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
                function(object $object){
                    return $object->foo(); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(0, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
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
                "1 named parameter. \$a is optional with default value 42. 1 passed argument value.",
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
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
                function(object $object){
                    return $object->foo(64); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(0, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            64,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
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
                "3 named parameters. All required. 3 passed argument values.",
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
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
                function(object $object){
                    return $object->foo(42, "bar", 3.14); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            "bar",
                            3.14,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(0, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                "3 named parameters. All required. 4 passed argument values.",
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
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
                function(object $object){
                    return $object->foo(42, "bar", 3.14, true); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            "bar",
                            3.14,
                            true,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(4, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(0, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                "3 named parameters. \$c is optional with default value being null. 2 passed argument values.",
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
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
                function(object $object){
                    return $object->foo(42, "bar"); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            "bar",
                            null,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                implode("", [
                    "3 named parameters. \$c is optional and default value being a global constant",
                    ", EBOREUM_EXCEPTIONAL_TEST_323586a4460042c286a544d258337226. 2 passed argument values.",
                ]),
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
                         */
                        public function foo(int $a, string $b, string $c = \EBOREUM_EXCEPTIONAL_TEST_323586a4460042c286a544d258337226): array
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
                function(object $object){
                    return $object->foo(42, "bar"); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            "bar",
                            \EBOREUM_EXCEPTIONAL_TEST_323586a4460042c286a544d258337226,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                implode("", [
                    "3 named parameters. \$c is optional and default value being a global constant",
                    ", EBOREUM_EXCEPTIONAL_TEST_323586a4460042c286a544d258337226. 3 passed argument values.",
                ]),
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
                         */
                        public function foo(int $a, string $b, string $c = \EBOREUM_EXCEPTIONAL_TEST_323586a4460042c286a544d258337226): array
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
                function(object $object){
                    return $object->foo(42, "bar", "baz"); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            "bar",
                            "baz",
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                implode("", [
                    "3 named parameters. \$c is optional and default value being a namespaced constant",
                    ", EBOREUM_EXCEPTIONAL_TEST_2098a8136eb848ce8d23f0e42a5d8a7a. 2 passed argument values.",
                ]),
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
                         */
                        public function foo(int $a, string $b, string $c = EBOREUM_EXCEPTIONAL_TEST_2098a8136eb848ce8d23f0e42a5d8a7a): array
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
                function(object $object){
                    return $object->foo(42, "bar"); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            "bar",
                            EBOREUM_EXCEPTIONAL_TEST_2098a8136eb848ce8d23f0e42a5d8a7a,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                implode("", [
                    "3 named parameters. \$c is optional and default value being a namespaced constant",
                    ", EBOREUM_EXCEPTIONAL_TEST_2098a8136eb848ce8d23f0e42a5d8a7a. 3 passed argument values.",
                ]),
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
                         */
                        public function foo(int $a, string $b, string $c = EBOREUM_EXCEPTIONAL_TEST_2098a8136eb848ce8d23f0e42a5d8a7a): array
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
                function(object $object){
                    return $object->foo(42, "bar", "baz"); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            "bar",
                            "baz",
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                implode("", [
                    "3 named parameters. \$c is optional and default value being a same-class constant, self::BAR.",
                    " being public. 2 passed argument values.",
                ]),
                function(){
                    return new class
                    {
                        public const BAR = 3.14;

                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
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
                function(object $object){
                    return $object->foo(42, "bar"); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            "bar",
                            3.14,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                implode("", [
                    "3 named parameters. \$c is optional and default value being a same-class constant, self::BAR.",
                    " being private. 2 passed argument values.",
                ]),
                function(){
                    return new class
                    {
                        private const BAR = 3.14;

                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
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
                function(object $object){
                    return $object->foo(42, "bar"); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            "bar",
                            3.14,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                implode("", [
                    "3 named parameters. \$c is optional and default value being a same-class constant, self::BAR.",
                    " 3 passed argument values, overriding \$c.",
                ]),
                function(){
                    return new class
                    {
                        public const BAR = 3.14;

                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
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
                function(object $object){
                    return $object->foo(42, "bar", 2.72); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            "bar",
                            2.72,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                implode("", [
                    "3 named parameters. \$c is optional and default value being a parent class constant",
                    ", using parent binding, parent::ATOM. 2 passed argument values.",
                ]),
                function(){
                    return new class extends \DateTimeImmutable
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
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
                function(object $object){
                    return $object->foo(42, "bar"); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            "bar",
                            \DateTimeInterface::ATOM,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                implode("", [
                    "3 named parameters. \$c is optional and default value being a parent class constant",
                    ", using parent binding, parent::ATOM. 3 passed argument values.",
                ]),
                function(){
                    return new class extends \DateTimeImmutable
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
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
                function(object $object){
                    return $object->foo(42, "bar", "baz"); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            "bar",
                            "baz",
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                implode("", [
                    "3 named parameters. \$c is optional and default value being a parent class constant",
                    ", using parent binding, \DateTimeInterface::ATOM. 2 passed argument values.",
                ]),
                function(){
                    return new class extends \DateTimeImmutable
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
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
                function(object $object){
                    return $object->foo(42, "bar"); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            "bar",
                            \DateTimeInterface::ATOM,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                implode("", [
                    "3 named parameters. \$c is optional and default value being a parent class constant",
                    ", using parent binding, \DateTimeImmutable::ATOM. 2 passed argument values.",
                    " Notice: \DateTimeImmutable - not \DateTimeInterface - is used here.",
                ]),
                function(){
                    return new class extends \DateTimeImmutable
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
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
                function(object $object){
                    return $object->foo(42, "bar"); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            "bar",
                            \DateTimeInterface::ATOM,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                implode("", [
                    "3 named parameters. \$c is optional and default value being a parent class constant",
                    ", using parent binding, \DateTimeInterface::ATOM. 3 passed argument values.",
                ]),
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
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
                function(object $object){
                    return $object->foo(42, "bar", "baz"); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            "bar",
                            "baz",
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                implode("", [
                    "1 named parameters. \$a is variadic. 0 passed argument values.",
                ]),
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
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
                function(object $object){
                    return $object->foo(); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(0, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            [],
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
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
                implode("", [
                    "1 named parameters. \$a is variadic. 1 passed argument values.",
                ]),
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
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
                function(object $object){
                    return $object->foo(...[1,2,3]); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){
                    $this->assertSame(0, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            [1,2,3],
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(1, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(1, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
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
                implode("", [
                    "3 named parameters. \$c is variadic. 0 passed argument values.",
                ]),
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
                         */
                        public function foo(int $a = 42, string $b = "baz", float ...$c): array
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
                function(object $object){
                    return $object->foo(); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){

                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            "baz",
                            [],
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                implode("", [
                    "3 named parameters. \$c is variadic. 2 passed argument values.",
                ]),
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
                         */
                        public function foo(int $a = 42, string $b = "baz", float ...$c): array
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
                function(object $object){
                    return $object->foo(43, "bim"); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){

                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            43,
                            "bim",
                            [],
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                implode("", [
                    "3 named parameters. \$c is variadic. 3 passed argument values.",
                ]),
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
                         */
                        public function foo(int $a = 42, string $b = "baz", float ...$c): array
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
                function(object $object){
                    return $object->foo(43, "bim", ...[1.0,2.0,3.0]); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){

                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            43,
                            "bim",
                            [1.0,2.0,3.0],
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
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
                implode("", [
                    "3 named parameters. \$b has default value, but \$a and \$c do not. 3 passed argument values.",
                ]),
                function(){
                    return new class
                    {
                        /**
                         * @return array{0: \ReflectionMethod, 1: array<int, mixed>, 2: MethodArgumentDiscloser}
                         */
                        public function foo(int $a, string $b = "baz", float $c): array
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
                function(object $object){
                    return $object->foo(43, "bim", 3.14); /** @phpstan-ignore-line */
                },
                function(string $message, MethodArgumentDiscloser $methodArgumentDiscloser, object $object){

                    $this->assertSame(2, $methodArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $methodArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            43,
                            "bim",
                            3.14,
                        ],
                        $methodArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(3, $methodArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(), $message);
                    $this->assertSame(0, $methodArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame("a", $methodArgumentDiscloser->getReflectionParameterByIndex(0)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame("b", $methodArgumentDiscloser->getReflectionParameterByIndex(1)->getName(), $message);
                    $this->assertNotNull($methodArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame("c", $methodArgumentDiscloser->getReflectionParameterByIndex(2)->getName(), $message);
                    $this->assertSame(null, $methodArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        3,
                        $methodArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $methodArgumentDiscloser->isLastNamedParameterVariadic(), $message);
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
    ): void
    {
        try {
            new MethodArgumentDiscloser(Caster::getInstance(), $reflectionMethod, []);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode("", [
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
                    preg_quote(MethodArgumentDiscloser::class, "/"),
                    preg_quote(Caster::class, "/"),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode("", [
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
                    preg_quote(basename(__FILE__), "/"),
                    preg_quote($glue, "/"),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(is_null($currentException));

            return;
        }

        $this->fail("Exception was never thrown.");
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: object}>
     */
    public function dataProvider_testConstructorThrowsExceptionWhenArgumentMethodArgumentValuesContainsFewerElementsThanTheNumberOfRequiredParametersInArgumentReflectionMethod(): array
    {
        return [
            [
                "Non-static",
                "->",
                (function(){
                    $object = new class
                    {
                        public function foo(int $a): \ReflectionMethod
                        {
                            $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);

                            return $reflectionMethod;
                        }
                    };

                    return $object->foo(42);
                })(),
            ],
            [
                "Static",
                "::",
                (function(){
                    $object = new class
                    {
                        public static function foo(int $a): \ReflectionMethod
                        {
                            $reflectionMethod = new \ReflectionMethod(static::class, __FUNCTION__);

                            return $reflectionMethod;
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
                    implode("", [
                        '/',
                        '^',
                        'Expects argument \$reflectionParameter \(name: "a"\) to have a default value available',
                        ', but it does not',
                        '$',
                        '/',
                    ]),
                    preg_quote(MethodArgumentDiscloser::class, "/"),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(is_null($currentException));

            return;
        }

        $this->fail("Exception was never thrown.");
    }

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenConstantNameDoesNotMatchRegularExpression(): void
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
            ->getMockBuilder("ReflectionParameter")
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method("isDefaultValueAvailable")
            ->with()
            ->willReturn(true);

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method("isDefaultValueConstant")
            ->with()
            ->willReturn(true);

        $reflectionParameter
            ->expects($this->exactly(2))
            ->method("getDefaultValueConstantName")
            ->with()
            ->willReturn("  I don't work as a constant name  ");

        $reflectionParameter
            ->expects($this->exactly(2))
            ->method("getName")
            ->with()
            ->willReturn("foo");

        $reflectionParameter
            ->expects($this->exactly(2))
            ->method("getDeclaringClass")
            ->with()
            ->willReturn($methodArgumentDiscloser->getReflectionFunction()->getDeclaringClass());

        $reflectionParameter
            ->expects($this->exactly(4))
            ->method("getDeclaringFunction")
            ->with()
            ->willReturn($methodArgumentDiscloser->getReflectionFunction());

        try {
            $methodArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode("", [
                        '/',
                        '^',
                        'Parameter \$foo in method class@anonymous\/in\/.+\/%s:\d+->foo',
                        ' has a default value, which is a constant, but a problem with this constant was encountered',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), "/"),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode("", [
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
            $this->assertTrue(is_null($currentException));

            return;
        }

        $this->fail("Exception was never thrown.");
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
            ->getMockBuilder("ReflectionParameter")
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method("isDefaultValueAvailable")
            ->with()
            ->willReturn(true);

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method("isDefaultValueConstant")
            ->with()
            ->willReturn(true);

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method("getDefaultValueConstantName")
            ->with()
            ->willReturn("NONEXSITING_CONSTANT_1aedab95b22c45afbdd0e5cf93af5ee9");

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method("getName")
            ->with()
            ->willReturn("foo");

        $reflectionParameter
            ->expects($this->exactly(2))
            ->method("getDeclaringClass")
            ->with()
            ->willReturn($methodArgumentDiscloser->getReflectionFunction()->getDeclaringClass());

        $reflectionParameter
            ->expects($this->exactly(4))
            ->method("getDeclaringFunction")
            ->with()
            ->willReturn($methodArgumentDiscloser->getReflectionFunction());

        try {
            $methodArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode("", [
                        '/',
                        '^',
                        'Parameter \$foo in method class@anonymous\/in\/.+\/%s:\d+->foo',
                        ' has a default value, which is a constant, but a problem with this constant was encountered',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), "/"),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode("", [
                    '/',
                    '^',
                    'The global constant "NONEXSITING_CONSTANT_1aedab95b22c45afbdd0e5cf93af5ee9" is not defined',
                    '$',
                    '/',
                ]),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(is_null($currentException));

            return;
        }

        $this->fail("Exception was never thrown.");
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
            ->getMockBuilder("ReflectionParameter")
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method("isDefaultValueAvailable")
            ->with()
            ->willReturn(true);

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method("isDefaultValueConstant")
            ->with()
            ->willReturn(true);

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method("getDefaultValueConstantName")
            ->with()
            ->willReturn("Foo\\Bar\\NONEXSITING_CONSTANT_e68ff2bd2d214c59abb3ad374163871f");

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method("getName")
            ->with()
            ->willReturn("foo");

        $reflectionParameter
            ->expects($this->exactly(2))
            ->method("getDeclaringClass")
            ->with()
            ->willReturn($methodArgumentDiscloser->getReflectionFunction()->getDeclaringClass());

        $reflectionParameter
            ->expects($this->exactly(4))
            ->method("getDeclaringFunction")
            ->with()
            ->willReturn($methodArgumentDiscloser->getReflectionFunction());

        try {
            $methodArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode("", [
                        '/',
                        '^',
                        'Parameter \$foo in method class@anonymous\/in\/.+\/%s:\d+->foo',
                        ' has a default value, which is a constant, but a problem with this constant was encountered',
                        '$',
                        '/',
                    ]),
                    preg_quote(basename(__FILE__), "/"),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                implode("", [
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
            $this->assertTrue(is_null($currentException));

            return;
        }

        $this->fail("Exception was never thrown.");
    }
}
