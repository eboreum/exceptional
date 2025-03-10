<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional;

use Closure;
use DateTimeInterface;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\FunctionArgumentDiscloser;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionFunction;
use ReflectionObject;

use function count;
use function func_get_args;
use function implode;
use function preg_quote;
use function sprintf;
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_0632691243674084af85b52269f0d4d2; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_1318db58f81f45c8a955f860c371ae5c; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_1863be0363a14f498ae9e8368267db83; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_1ca3717f657946cc8ea73a9c10d25a15; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_26670d45e52341889d9dd9d9a2026810; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_273f629332064648a935524ecf024cc9; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_37704407c9d04b5dbf2ce6de4ffbbfbd; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_42fb127ea64c4bc39f6d0ce58df1b9a6; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_445cb914ff6f48a0a039e4eedd0f4ff0; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_534d34186ec84bd5baf195e141284d36; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_55f325c24dc64ff4bb9df02b6f51de6d; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_5d337039b3b747738ecfaf56520a5450; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_85366d3d2de04a969f58caf818a35590; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_8ff1bec0e2734ff5b74e095ae01cd3da; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_912de21dd0fd454f8cdb0b71ac45a9e3; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_a822fb8b9ffd444b923b71185d41ad57; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_b50e80c0945c44e98bd73f356410e342; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_d89b416e02504e34812c70ae20083403; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_d9d24ee6520f4a2792f07471f77eaf45; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_e1508b2e20334bd5a4de82855086873e; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_f169b74a249c47f28543063439f58f4d; // phpcs:ignore
use function TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\foo_fb4c857d2c2b422da8d8e8fc6ed7da1c; // phpcs:ignore

use const EBOREUM_EXCEPTIONAL_TEST_3AE1CC1DE032441D9A2AC7929B9D9892;
use const TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist\EBOREUM_EXCEPTIONAL_TEST_E000D6A7BA5941278D823905F218B71F; // phpcs:ignore

#[CoversClass(FunctionArgumentDiscloser::class)]
class FunctionArgumentDiscloserTest extends TestCase
{
    /**
     * @return array<
     *   array{
     *     string,
     *     Closure():array{ReflectionFunction, array<mixed>, FunctionArgumentDiscloser},
     *     Closure(self, string, FunctionArgumentDiscloser):void,
     *   }
     * >
     */
    public static function providerTestBasics(): array
    {
        return [
            [
                '0 named parameters. 0 passed argument values.',
                static function (): array {
                    return foo_a822fb8b9ffd444b923b71185d41ad57();
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(-1, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(0, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        0,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(0, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        0,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '1 named parameter. $a is optional with default value 42. 0 passed argument values.',
                static function (): array {
                    return foo_8ff1bec0e2734ff5b74e095ae01cd3da();
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(0, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(1, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [42],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        1,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        0,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '1 named parameter. $a is optional with default value 42. 1 passed argument value.',
                static function (): array {
                    return foo_f169b74a249c47f28543063439f58f4d(64);
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(0, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(1, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [64],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        1,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        0,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. All required. 3 passed argument values.',
                static function (): array {
                    return foo_d89b416e02504e34812c70ae20083403(42, 'bar', 3.14);
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [
                            42,
                            'bar',
                            3.14,
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(0, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $self->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $self->assertSame(
                        3,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. All required. 4 passed argument values.',
                static function (): array {
                    return foo_26670d45e52341889d9dd9d9a2026810(42, 'bar', 3.14, true);
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [
                            42,
                            'bar',
                            3.14,
                            true,
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        4,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(0, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $self->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $self->assertSame(
                        3,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. $c is optional with default value being null. 2 passed argument values.',
                static function (): array {
                    return foo_1863be0363a14f498ae9e8368267db83(42, 'bar');
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [
                            42,
                            'bar',
                            null,
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $self->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $self->assertSame(
                        2,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a global constant',
                    ', EBOREUM_EXCEPTIONAL_TEST_3AE1CC1DE032441D9A2AC7929B9D9892. 2 passed argument values.',
                ]),
                static function (): array {
                    return foo_0632691243674084af85b52269f0d4d2(42, 'bar');
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [
                            42,
                            'bar',
                            EBOREUM_EXCEPTIONAL_TEST_3AE1CC1DE032441D9A2AC7929B9D9892,
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $self->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $self->assertSame(
                        2,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a global constant',
                    ', EBOREUM_EXCEPTIONAL_TEST_3AE1CC1DE032441D9A2AC7929B9D9892. 3 passed argument values.',
                ]),
                static function (): array {
                    return foo_273f629332064648a935524ecf024cc9(42, 'bar', 'baz');
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [
                            42,
                            'bar',
                            'baz',
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $self->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $self->assertSame(
                        2,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a namespaced constant',
                    ', EBOREUM_EXCEPTIONAL_TEST_E000D6A7BA5941278D823905F218B71F. 2 passed argument values.',
                ]),
                static function (): array {
                    return foo_5d337039b3b747738ecfaf56520a5450(42, 'bar');
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [
                            42,
                            'bar',
                            EBOREUM_EXCEPTIONAL_TEST_E000D6A7BA5941278D823905F218B71F,
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $self->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $self->assertSame(
                        2,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being a namespaced constant',
                    ', EBOREUM_EXCEPTIONAL_TEST_E000D6A7BA5941278D823905F218B71F. 3 passed argument values.',
                ]),
                static function (): array {
                    return foo_b50e80c0945c44e98bd73f356410e342(42, 'bar', 'baz');
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser
                ): void {
                    $self->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [
                            42,
                            'bar',
                            'baz',
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $self->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $self->assertSame(
                        2,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being \DateTimeInterface::ATOM.',
                    ' 2 passed argument values.',
                ]),
                static function (): array {
                    return foo_fb4c857d2c2b422da8d8e8fc6ed7da1c(42, 'bar');
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [
                            42,
                            'bar',
                            DateTimeInterface::ATOM,
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $self->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $self->assertSame(
                        2,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being  \DateTimeImmutable::ATOM. 2 passed',
                    ' argument values.',
                    ' Notice: \DateTimeImmutable - not \DateTimeInterface - is used here.',
                ]),
                static function (): array {
                    return foo_85366d3d2de04a969f58caf818a35590(42, 'bar');
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [
                            42,
                            'bar',
                            DateTimeInterface::ATOM,
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $self->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $self->assertSame(
                        2,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                implode('', [
                    '3 named parameters. $c is optional and default value being  \DateTimeInterface::ATOM.',
                    ' 3 passed argument values.',
                ]),
                static function (): array {
                    return foo_1ca3717f657946cc8ea73a9c10d25a15(42, 'bar', 'baz');
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [
                            42,
                            'bar',
                            'baz',
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $self->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $self->assertSame(
                        2,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '1 named parameters. $a is variadic. 0 passed argument values.',
                static function (): array {
                    return foo_1318db58f81f45c8a955f860c371ae5c();
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(0, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(1, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [
                            [],
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        1,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        0,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(true, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '1 named parameters. $a is variadic. 1 passed argument values.',
                static function (): array {
                    return foo_e1508b2e20334bd5a4de82855086873e(...[1, 2, 3]);
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(0, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(1, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [
                            [1, 2, 3],
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        1,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(1, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        0,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(true, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. $c is variadic. 0 passed argument values.',
                static function (): array {
                    return foo_534d34186ec84bd5baf195e141284d36();
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [
                            42,
                            'baz',
                            [],
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(3, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $self->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $self->assertSame(
                        0,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(true, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. $c is variadic. 2 passed argument values.',
                static function (): array {
                    return foo_37704407c9d04b5dbf2ce6de4ffbbfbd(43, 'bim');
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [
                            43,
                            'bim',
                            [],
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(3, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $self->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $self->assertSame(
                        0,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(true, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                '3 named parameters. $c is variadic. 3 passed argument values.',
                static function (): array {
                    return foo_42fb127ea64c4bc39f6d0ce58df1b9a6(43, 'bim', ...[1.0, 2.0, 3.0]);
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [
                            43,
                            'bim',
                            [1.0,2.0,3.0],
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(3, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $self->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $self->assertSame(
                        0,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(true, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
            [
                'An anonymous function. 3 named parameters. 3 passed argument values.',
                static function (): array {
                    /**
                     * @return array{0: ReflectionFunction, 1: array<int, mixed>, 2: FunctionArgumentDiscloser}
                     */
                    $fooec59a7b7151f481fa3c3b97b1d0e84f1 = static function (
                        int $a,
                        string $b,
                        float $c
                    ) use (&$fooec59a7b7151f481fa3c3b97b1d0e84f1) {
                        $reflectionFunction = new ReflectionFunction($fooec59a7b7151f481fa3c3b97b1d0e84f1);
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

                    return $fooec59a7b7151f481fa3c3b97b1d0e84f1(43, 'bim', 3.14);
                },
                static function (
                    self $self,
                    string $message,
                    FunctionArgumentDiscloser $functionArgumentDiscloser,
                ): void {
                    $self->assertSame(2, $functionArgumentDiscloser->getLastNamedParameterIndex(), $message);
                    $self->assertSame(3, $functionArgumentDiscloser->getNamedParameterCount(), $message);
                    $self->assertSame(
                        [
                            43,
                            'bim',
                            3.14,
                        ],
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValues(),
                        $message,
                    );
                    $self->assertSame(
                        3,
                        $functionArgumentDiscloser->getNormalizedFunctionArgumentValuesCount(),
                        $message,
                    );
                    $self->assertSame(0, $functionArgumentDiscloser->getOptionalParameterCount(), $message);
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(-1), $message);
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(0), $message);
                    $self->assertSame(
                        'a',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(0)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(1), $message);
                    $self->assertSame(
                        'b',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(1)->getName(),
                        $message,
                    );
                    $self->assertNotNull($functionArgumentDiscloser->getReflectionParameterByIndex(2), $message);
                    $self->assertSame(
                        'c',
                        $functionArgumentDiscloser->getReflectionParameterByIndex(2)->getName(),
                        $message,
                    );
                    $self->assertSame(null, $functionArgumentDiscloser->getReflectionParameterByIndex(3), $message);
                    $self->assertSame(
                        3,
                        $functionArgumentDiscloser->getReflectionFunction()->getNumberOfRequiredParameters(),
                        $message,
                    );
                    $self->assertSame(false, $functionArgumentDiscloser->isLastNamedParameterVariadic(), $message);
                },
            ],
        ];
    }

    /**
     * @param Closure():array{ReflectionFunction, array<mixed>, FunctionArgumentDiscloser} $factory
     * @param Closure(self, string, FunctionArgumentDiscloser):void $assertionsCallback
     */
    #[DataProvider('providerTestBasics')]
    public function testBasics(string $message, Closure $factory, Closure $assertionsCallback): void
    {
        [
            $reflectionFunction,
            $functionArgumentValues,
            $functionArgumentDiscloser,
        ] = $factory();

        $this->assertSame($reflectionFunction, $functionArgumentDiscloser->getReflectionFunction(), $message);
        $this->assertSame($functionArgumentValues, $functionArgumentDiscloser->getFunctionArgumentValues(), $message);
        $this->assertSame(
            count($functionArgumentValues),
            $functionArgumentDiscloser->getFunctionArgumentValuesCount(),
            $message,
        );

        $assertionsCallback($this, $message, $functionArgumentDiscloser);
    }

    /**
     * @param array<int, int> $functionArgumentValues
     * @param Closure():ReflectionFunction $callback
     */
    #[DataProvider('providerTestConstructorThrowsExceptionWhenArgumentMethodArgumentValuesContainsFewerElementsThanTheNumberOfRequiredParametersInArgumentReflectionFunction')] // phpcs:ignore
    public function testConstructorThrowsExceptionWhenArgumentMethodArgumentValuesContainsFewerElementsThanTheNumberOfRequiredParametersInArgumentReflectionFunction( // phpcs:ignore
        int $expectedPassedArgumentCount,
        int $expectedNamedArgumentCount,
        string $expectedFunctionArgumentValuesStr,
        array $functionArgumentValues,
        Closure $callback
    ): void {
        $reflectionFunction = $callback();

        try {
            new FunctionArgumentDiscloser(Caster::getInstance(), $reflectionFunction, $functionArgumentValues);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
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
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
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
     * @return array<int, array{int, int, string, array<int, int>, Closure():ReflectionFunction}>
     */
    public static function providerTestConstructorThrowsExceptionWhenArgumentMethodArgumentValuesContainsFewerElementsThanTheNumberOfRequiredParametersInArgumentReflectionFunction(): array // phpcs:ignore
    {
        return [
            [
                0,
                1,
                '(array(0)) []',
                [],
                static function () {
                    return new ReflectionFunction(
                        implode('', [
                            'TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest',
                            '\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespaced',
                            'ConstantDoesNotExist\foo_4d2650269a324a3788f827ee739afee1',
                        ]),
                    );
                },
            ],
            [
                2,
                4,
                '(array(2)) [(int) 0 => (int) 42, (int) 1 => (int) 43]',
                [42, 43],
                static function () {
                    return new ReflectionFunction(
                        implode('', [
                            'TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest',
                            '\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespaced',
                            'ConstantDoesNotExist\foo_fe25fbdda555464f982783f37b43ade9',
                        ]),
                    );
                },
            ],
        ];
    }

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenNoDefaultValueIsAvailableOnReflectionParameter(): void // phpcs:ignore
    {
        $functionArgumentDiscloser = foo_912de21dd0fd454f8cdb0b71ac45a9e3(42);

        try {
            $functionArgumentDiscloser->getDefaultValueForReflectionParameter(
                $functionArgumentDiscloser->getReflectionFunction()->getParameters()[0]
            );
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
            $this->assertMatchesRegularExpression(
                implode('', [
                    '/',
                    '^',
                    'Expects argument \$reflectionParameter \(name: "a"\) to have a default value available',
                    ', but it does not',
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

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenConstantNameDoesNotMatchRegularExpression(): void // phpcs:ignore
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
            ->expects($this->exactly(1))
            ->method('getDefaultValueConstantName')
            ->with()
            ->willReturn('  I don\'t work as a constant name  ');

        $reflectionParameter
            ->expects($this->exactly(2))
            ->method('getName')
            ->with()
            ->willReturn('foo');

        $reflectionParameter
            ->expects($this->exactly(1))
            ->method('getDeclaringFunction')
            ->with()
            ->willReturn($functionArgumentDiscloser->getReflectionFunction());

        try {
            $functionArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
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
                    preg_quote(
                        implode('', [
                            'TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest',
                            '\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespaced',
                            'ConstantDoesNotExist',
                        ]),
                        '/',
                    ),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
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

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedGlobalConstantDoesNotExist(): void // phpcs:ignore
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
            ->expects($this->exactly(1))
            ->method('getDeclaringFunction')
            ->with()
            ->willReturn($functionArgumentDiscloser->getReflectionFunction());

        try {
            $functionArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
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
                    preg_quote(
                        implode('', [
                            'TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest',
                            '\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstant',
                            'DoesNotExist',
                        ]),
                        '/',
                    ),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
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

    public function testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist(): void // phpcs:ignore
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
            ->expects($this->exactly(1))
            ->method('getDeclaringFunction')
            ->with()
            ->willReturn($functionArgumentDiscloser->getReflectionFunction());

        try {
            $functionArgumentDiscloser->getDefaultValueForReflectionParameter($reflectionParameter);
        } catch (Exception $e) {
            $currentException = $e;
            $this->assertSame(RuntimeException::class, $currentException::class);
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
                    preg_quote((new ReflectionObject($this))->getShortName(), '/'),
                    preg_quote(__FUNCTION__, '/'),
                ),
                $currentException->getMessage(),
            );

            $currentException = $currentException->getPrevious();
            $this->assertIsObject($currentException);
            $this->assertSame(RuntimeException::class, $currentException::class);
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
