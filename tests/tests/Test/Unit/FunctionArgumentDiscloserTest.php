<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional;

use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\FunctionArgumentDiscloser;
use PHPUnit\Framework\TestCase;

use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_0632691243674084af85b52269f0d4d2;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_1318db58f81f45c8a955f860c371ae5c;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_1863be0363a14f498ae9e8368267db83;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_1ca3717f657946cc8ea73a9c10d25a15;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_26670d45e52341889d9dd9d9a2026810;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_273f629332064648a935524ecf024cc9;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_37704407c9d04b5dbf2ce6de4ffbbfbd;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_42fb127ea64c4bc39f6d0ce58df1b9a6;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_445cb914ff6f48a0a039e4eedd0f4ff0;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_4d2650269a324a3788f827ee739afee1;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_534d34186ec84bd5baf195e141284d36;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_55f325c24dc64ff4bb9df02b6f51de6d;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_5d337039b3b747738ecfaf56520a5450;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_85366d3d2de04a969f58caf818a35590;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_8ff1bec0e2734ff5b74e095ae01cd3da;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_912de21dd0fd454f8cdb0b71ac45a9e3;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_a822fb8b9ffd444b923b71185d41ad57;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_b50e80c0945c44e98bd73f356410e342;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_d89b416e02504e34812c70ae20083403;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_d9d24ee6520f4a2792f07471f77eaf45;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_e1508b2e20334bd5a4de82855086873e;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_f169b74a249c47f28543063439f58f4d;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_fb4c857d2c2b422da8d8e8fc6ed7da1c;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_fe25fbdda555464f982783f37b43ade9;

class FunctionArgumentDiscloserTest extends TestCase
{
    /**
     * @dataProvider dataProvider_testBasics
     */
    public function testBasics(string $message, \Closure $valueFactoryCallback, \Closure $assertionsCallback ): void
    {
        [
            $reflectionFunction,
            $functionArgumentValues,
            $functionArgumentDiscloser
        ] = $valueFactoryCallback();

        $this->assertSame($reflectionFunction, $functionArgumentDiscloser->getReflectionFunction(), $message);
        $this->assertSame($functionArgumentValues, $functionArgumentDiscloser->getFunctionArgumentValues(), $message);
        $this->assertSame(
            count($functionArgumentValues),
            $functionArgumentDiscloser->getFunctionArgumentValuesCount(),
            $message,
        );

        $assertionsCallback($message, $functionArgumentDiscloser);
    }

    /**
     * @return array<array{string, \Closure, \Closure}>
     */
    public function dataProvider_testBasics(): array
    {
        return [
            [
                '0 named parameters. 0 passed argument values.',
                static function () {
                    return foo_a822fb8b9ffd444b923b71185d41ad57();
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(-1, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(0, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        0,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(0, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        0,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '1 named parameter. $a is optional with default value 42. 0 passed argument values.',
                static function () {
                    return foo_8ff1bec0e2734ff5b74e095ae01cd3da();
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(0, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(1, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [42],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        1,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        0,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '1 named parameter. $a is optional with default value 42. 1 passed argument value.',
                static function () {
                    return foo_f169b74a249c47f28543063439f58f4d(64);
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(0, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(1, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [64],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        1,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        0,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. All required. 3 passed argument values.',
                static function () {
                    return foo_d89b416e02504e34812c70ae20083403(42, 'bar', 3.14);
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            3.14,
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(0, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        3,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. All required. 4 passed argument values.',
                static function () {
                    return foo_26670d45e52341889d9dd9d9a2026810(42, 'bar', 3.14, true);
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            3.14,
                            true,
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        4,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(0, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        3,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. $c is optional with default value being null. 2 passed argument values.',
                static function () {
                    return foo_1863be0363a14f498ae9e8368267db83(42, 'bar');
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            null,
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a global constant',
                    ', EBOREUM_EXCEPTIONAL_TEST_3ae1cc1de032441d9a2ac7929b9d9892. 2 passed argument values.',
                ]),
                static function () {
                    return foo_0632691243674084af85b52269f0d4d2(42, 'bar');
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            \EBOREUM_EXCEPTIONAL_TEST_3ae1cc1de032441d9a2ac7929b9d9892,
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a global constant',
                    ', EBOREUM_EXCEPTIONAL_TEST_3ae1cc1de032441d9a2ac7929b9d9892. 3 passed argument values.',
                ]),
                static function () {
                    return foo_273f629332064648a935524ecf024cc9(42, 'bar', 'baz');
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            'baz',
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a namespaced constant',
                    ', EBOREUM_EXCEPTIONAL_TEST_e000d6a7ba5941278d823905f218b71f. 2 passed argument values.',
                ]),
                static function () {
                    return foo_5d337039b3b747738ecfaf56520a5450(42, 'bar');
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            \TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\EBOREUM_EXCEPTIONAL_TEST_e000d6a7ba5941278d823905f218b71f,
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a namespaced constant',
                    ', EBOREUM_EXCEPTIONAL_TEST_e000d6a7ba5941278d823905f218b71f. 3 passed argument values.',
                ]),
                static function () {
                    return foo_b50e80c0945c44e98bd73f356410e342(42, 'bar', 'baz');
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            'baz',
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being \DateTimeInterface::ATOM.',
                    ' 2 passed argument values.',
                ]),
                static function () {
                    return foo_fb4c857d2c2b422da8d8e8fc6ed7da1c(42, 'bar');
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            \DateTimeInterface::ATOM,
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being  \DateTimeImmutable::ATOM. 2 passed',
                    ' argument values.',
                    ' Notice: \DateTimeImmutable - not \DateTimeInterface - is used here.',
                ]),
                static function () {
                    return foo_85366d3d2de04a969f58caf818a35590(42, 'bar');
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            \DateTimeInterface::ATOM,
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being  \DateTimeInterface::ATOM.',
                    ' 3 passed argument values.',
                ]),
                static function () {
                    return foo_1ca3717f657946cc8ea73a9c10d25a15(42, 'bar', 'baz');
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'bar',
                            'baz',
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        2,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '1 named parameters. $a is variadic. 0 passed argument values.',
                static function () {
                    return foo_1318db58f81f45c8a955f860c371ae5c();
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(0, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(1, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            [],
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        1,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        0,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(true, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '1 named parameters. $a is variadic. 1 passed argument values.',
                static function () {
                    return foo_e1508b2e20334bd5a4de82855086873e(...[1, 2, 3]);
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(0, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(1, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            [1, 2, 3],
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        1,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        0,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(true, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. $c is variadic. 0 passed argument values.',
                static function () {
                    return foo_534d34186ec84bd5baf195e141284d36();
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            42,
                            'baz',
                            [],
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(3, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        0,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(true, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. $c is variadic. 2 passed argument values.',
                static function () {
                    return foo_37704407c9d04b5dbf2ce6de4ffbbfbd(43, 'bim');
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            43,
                            'bim',
                            [],
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(3, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        0,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(true, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. $c is variadic. 3 passed argument values.',
                static function () {
                    return foo_42fb127ea64c4bc39f6d0ce58df1b9a6(43, 'bim', ...[1.0, 2.0, 3.0]);
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            43,
                            'bim',
                            [1.0,2.0,3.0],
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(3, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        0,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(true, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                'An anonymous function. 3 named parameters. 3 passed argument values.',
                static function () {
                    /**
                     * @return array{0: \ReflectionFunction, 1: array<int, mixed>, 2: FunctionArgumentDiscloser}
                     */
                    $foo_ec59a7b7151f481fa3c3b97b1d0e84f1 = static function (
                        int $a,
                        string $b,
                        float $c
                    ) use (&$foo_ec59a7b7151f481fa3c3b97b1d0e84f1) {
                        $reflectionFunction = new \ReflectionFunction($foo_ec59a7b7151f481fa3c3b97b1d0e84f1);
                        $functionArgumentValues = func_get_args();

                        return [
                            $reflectionFunction,
                            $functionArgumentValues,
                            new FunctionArgumentDiscloser(
                                Caster::getInstance(),
                                $reflectionFunction,
                                $functionArgumentValues
                            ),
                        ];
                    };

                    return $foo_ec59a7b7151f481fa3c3b97b1d0e84f1(43, 'bim', 3.14);
                },
                function (string $message, FunctionArgumentDiscloser $functionArgumentDiscloser): void {
                    $this->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $this->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $this->assertSame(
                        [
                            43,
                            'bim',
                            3.14,
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $this->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $this->assertSame(0, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $this->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $this->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $this->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $this->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $this->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $this->assertSame(
                        3,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $this->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
        ];
    }

    /**
     * @dataProvider dataProvider_testConstructorThrowsExceptionWhenArgumentMethodArgumentValuesContainsFewerElementsThanTheNumberOfRequiredParametersInArgumentReflectionFunction
     * @param array<int, int> $functionArgumentValues
     */
    public function testConstructorThrowsExceptionWhenArgumentMethodArgumentValuesContainsFewerElementsThanTheNumberOfRequiredParametersInArgumentReflectionFunction(
        int $expectedPassedArgumentCount,
        int $expectedNamedArgumentCount,
        string $expectedFunctionArgumentValuesStr,
        array $functionArgumentValues,
        \Closure $callback
    ): void {
        $reflectionFunction = $callback();

        try {
            new FunctionArgumentDiscloser(Caster::getInstance(), $reflectionFunction, $functionArgumentValues);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Failed to construct \\\\%s with arguments \{',
                            '\$caster = \(object\) \\\\%s',
                            ', \$reflectionFunction = \(object\) \\\\ReflectionFunction',
                            ', \$functionArgumentValues = %s',
                        '\}',
                        '$',
                        '/',
                    ]),
                    preg_quote(FunctionArgumentDiscloser::class, '/'),
                    preg_quote(Caster::class, '/'),
                    preg_quote($expectedFunctionArgumentValuesStr, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Argument \$functionArgumentValues = %s contains fewer elements \(%d\) than',
                        ' the required number of parameters \(%d\) in argument \$reflectionFunction = \(object\)',
                        ' \\\\ReflectionFunction, which is bogus',
                        '$',
                        '/',
                    ]),
                    preg_quote($expectedFunctionArgumentValuesStr, '/'),
                    $expectedPassedArgumentCount,
                    $expectedNamedArgumentCount,
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    /**
     * @return array<int, array{int, int, string, array<int, int>, \Closure}>
     */
    public function dataProvider_testConstructorThrowsExceptionWhenArgumentMethodArgumentValuesContainsFewerElementsThanTheNumberOfRequiredParametersInArgumentReflectionFunction(): array
    {
        return [
            [
                0,
                1,
                '(array(0)) []',
                [],
                static function () {
                    return new \ReflectionFunction('TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_4d2650269a324a3788f827ee739afee1');
                },
            ],
            [
                2,
                4,
                '(array(2)) [(int) 0 => (int) 42, (int) 1 => (int) 43]',
                [42, 43],
                static function () {
                    return new \ReflectionFunction('TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_fe25fbdda555464f982783f37b43ade9');
                },
            ],
        ];
    }

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenNoDefaultValueIsAvailableOnReflectionParameter(): void
    {
        $functionArgumentDiscloser = foo_912de21dd0fd454f8cdb0b71ac45a9e3(42);

        try {
            $functionArgumentDiscloser->getDefaultValueForReflectionParameter(
                $functionArgumentDiscloser->getReflectionFunction()->getParameters()[0]
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
                    preg_quote(FunctionArgumentDiscloser::class, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertTrue(null === $currentException);

            return;
        }

        $this->fail('Exception was never thrown.');
    }

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenConstantNameDoesNotMatchRegularExpression(): void
    {
        $functionArgumentDiscloser = foo_55f325c24dc64ff4bb9df02b6f51de6d(42);

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
            ->willReturn('  I don\'t work as a constant name  ');

        $reflectionParameter
            ->expects($this->exactly(2))
            ->method('getName')
            ->with()
            ->willReturn('foo');

        $reflectionParameter
            ->expects($this->exactly(2))
            ->method('getDeclaringFunction')
            ->with()
            ->willReturn($functionArgumentDiscloser->getReflectionFunction());

        try {
            $functionArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Parameter \$foo in function \\\\%s\\\\foo_55f325c24dc64ff4bb9df02b6f51de6d',
                        ' has a default value, which is a constant, but a problem with this constant was encountered',
                        '$',
                        '/',
                    ]),
                    preg_quote('TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist', '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
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
        $functionArgumentDiscloser = foo_445cb914ff6f48a0a039e4eedd0f4ff0(42);

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
            ->expects($this->exactly(1))
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
            ->method('getDeclaringFunction')
            ->with()
            ->willReturn($functionArgumentDiscloser->getReflectionFunction());

        try {
            $functionArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Parameter \$foo in function \\\\%s\\\\foo_445cb914ff6f48a0a039e4eedd0f4ff0',
                        ' has a default value, which is a constant, but a problem with this constant was encountered',
                        '$',
                        '/',
                    ]),
                    preg_quote('TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist', '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
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
        $functionArgumentDiscloser = foo_d9d24ee6520f4a2792f07471f77eaf45(42);

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
            ->expects($this->exactly(1))
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
            ->method('getDeclaringFunction')
            ->with()
            ->willReturn($functionArgumentDiscloser->getReflectionFunction());

        try {
            $functionArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (\Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, get_class($currentException));
            $this->assertMatchesRegularExpression(
                sprintf(
                    implode('', [
                        '/',
                        '^',
                        'Parameter \$foo in function (\\\\\w+)+\\\\%s\\\\%s\\\\foo_d9d24ee6520f4a2792f07471f77eaf45',
                        ' has a default value, which is a constant, but a problem with this constant was encountered',
                        '$',
                        '/',
                    ]),
                    preg_quote((new \ReflectionObject($this))->getShortName(), '/'),
                    preg_quote(__FUNCTION__, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
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
}
