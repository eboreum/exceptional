<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist; // phpcs:ignore

use DateTimeImmutable;
use DateTimeInterface;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\FunctionArgumentDiscloser;
use ReflectionFunction;

use function func_get_args;

use const EBOREUM_EXCEPTIONAL_TEST_3AE1CC1DE032441D9A2AC7929B9D9892;

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_0632691243674084af85b52269f0d4d2( // phpcs:ignore
    int $a,
    string $b,
    string $c = EBOREUM_EXCEPTIONAL_TEST_3AE1CC1DE032441D9A2AC7929B9D9892
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_1318db58f81f45c8a955f860c371ae5c( // phpcs:ignore
    int ...$a
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_1863be0363a14f498ae9e8368267db83( // phpcs:ignore
    int $a,
    string $b,
    ?float $c = null
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_1ca3717f657946cc8ea73a9c10d25a15( // phpcs:ignore
    int $a,
    string $b,
    string $c = DateTimeInterface::ATOM
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_26670d45e52341889d9dd9d9a2026810( // phpcs:ignore
    int $a,
    string $b,
    float $c,
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_273f629332064648a935524ecf024cc9( // phpcs:ignore
    int $a,
    string $b,
    string $c = EBOREUM_EXCEPTIONAL_TEST_3AE1CC1DE032441D9A2AC7929B9D9892
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_37704407c9d04b5dbf2ce6de4ffbbfbd( // phpcs:ignore
    int $a = 42,
    string $b = 'baz',
    float ...$c,
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_42fb127ea64c4bc39f6d0ce58df1b9a6( // phpcs:ignore
    int $a = 42,
    string $b = 'baz',
    float ...$c,
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

function foo_445cb914ff6f48a0a039e4eedd0f4ff0( // phpcs:ignore
    int $a,
): FunctionArgumentDiscloser {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);

    return new FunctionArgumentDiscloser(Caster::getInstance(), $reflectionFunction, [42]);
}

function foo_4d2650269a324a3788f827ee739afee1( // phpcs:ignore
    int $a,
): void {
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_534d34186ec84bd5baf195e141284d36( // phpcs:ignore
    int $a = 42,
    string $b = 'baz',
    float ...$c
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

function foo_55f325c24dc64ff4bb9df02b6f51de6d( // phpcs:ignore
    int $a,
): FunctionArgumentDiscloser {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);

    return new FunctionArgumentDiscloser(Caster::getInstance(), $reflectionFunction, [42]);
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_5d337039b3b747738ecfaf56520a5450( // phpcs:ignore
    int $a,
    string $b,
    string $c = EBOREUM_EXCEPTIONAL_TEST_E000D6A7BA5941278D823905F218B71F
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_85366d3d2de04a969f58caf818a35590( // phpcs:ignore
    int $a,
    string $b,
    string $c = DateTimeImmutable::ATOM
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_8ff1bec0e2734ff5b74e095ae01cd3da( // phpcs:ignore
    int $a = 42,
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

function foo_912de21dd0fd454f8cdb0b71ac45a9e3( // phpcs:ignore
    int $a,
): FunctionArgumentDiscloser {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);

    return new FunctionArgumentDiscloser(Caster::getInstance(), $reflectionFunction, [42]);
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_a822fb8b9ffd444b923b71185d41ad57(): array // phpcs:ignore
{
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_b50e80c0945c44e98bd73f356410e342( // phpcs:ignore
    int $a,
    string $b,
    string $c = EBOREUM_EXCEPTIONAL_TEST_E000D6A7BA5941278D823905F218B71F
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_d89b416e02504e34812c70ae20083403( // phpcs:ignore
    int $a,
    string $b,
    float $c,
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

function foo_d9d24ee6520f4a2792f07471f77eaf45( // phpcs:ignore
    int $a,
): FunctionArgumentDiscloser {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);

    return new FunctionArgumentDiscloser(Caster::getInstance(), $reflectionFunction, [42]);
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_e1508b2e20334bd5a4de82855086873e( // phpcs:ignore
    int ...$a,
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_f169b74a249c47f28543063439f58f4d( // phpcs:ignore
    int $a = 42,
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

/**
 * @return array{ReflectionFunction, array<int, mixed>, FunctionArgumentDiscloser}
 */
function foo_fb4c857d2c2b422da8d8e8fc6ed7da1c( // phpcs:ignore
    int $a,
    string $b,
    string $c = DateTimeInterface::ATOM
): array {
    $reflectionFunction = new ReflectionFunction(__FUNCTION__);
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
}

function foo_fe25fbdda555464f982783f37b43ade9( // phpcs:ignore
    int $a,
    int $b,
    int $c,
    int $d,
): void {
}
