<?php

declare(strict_types=1);

namespace Eboreum\Exceptional;

use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Caster\Contract\ImmutableObjectInterface;
use Eboreum\Exceptional\Exception\RuntimeException;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use Throwable;

use function array_key_exists;
use function array_slice;
use function array_values;
use function assert;
use function class_exists;
use function constant;
use function count;
use function defined;
use function escapeshellarg;
use function implode;
use function interface_exists;
use function is_object;
use function is_string;
use function max;
use function preg_match;
use function sprintf;

/**
 * Provides an API for extracting information about and mapping them to values for method arguments/parameters.
 *
 * You must NOT implement the interface TextuallyIdentifiableInterface on this class, because if done, things will get
 * cyclic and explode spectacularly.
 */
abstract class AbstractFunctionArgumentDiscloser implements ImmutableObjectInterface
{
    abstract public static function getDefaultValueConstantRegex(): string;

    protected CasterInterface $caster;

    protected ReflectionFunction|ReflectionMethod $reflectionFunction;

    /** @var array<int, mixed> */
    protected array $functionArgumentValues;

    /** @var array<int, mixed>|null */
    protected ?array $normalizedFunctionArgumentValues = null;

    protected ?int $requiredParameterCount = null;

    public function getCaster(): CasterInterface
    {
        return $this->caster;
    }

    /**
     * @param ReflectionParameter $reflectionParameter
     *                                          Must have a default value. Otherwise, a RuntimeException is thrown.
     *
     * @throws RuntimeException
     */
    public function getDefaultValueForReflectionParameter(ReflectionParameter $reflectionParameter): mixed
    {
        if (false === $reflectionParameter->isDefaultValueAvailable()) {
            throw new RuntimeException(sprintf(
                'Expects argument $reflectionParameter (name: %s) to have a default value available, but it does not',
                $this->getCaster()->cast($reflectionParameter->getName()),
            ));
        }

        if ($reflectionParameter->isDefaultValueConstant()) {
            try {
                $defaultValueConstantName = $reflectionParameter->getDefaultValueConstantName();

                assert(is_string($defaultValueConstantName));

                preg_match(
                    static::getDefaultValueConstantRegex(),
                    $defaultValueConstantName,
                    $match,
                );

                if (!$match) {
                    throw new RuntimeException(sprintf(
                        implode('', [
                            'Expects default value of parameter $%s - a constant - to match',
                            ' regular expression %s, but it does not. Found: %s',
                        ]),
                        $reflectionParameter->getName(),
                        escapeshellarg(static::getDefaultValueConstantRegex()),
                        $this->getCaster()->castTyped($defaultValueConstantName),
                    ));
                }

                foreach (['globalName', 'namespacedName'] as $key) {
                    if ($match[$key] ?? false) {
                        if (false === defined($match[$key])) {
                            throw new RuntimeException(sprintf(
                                'The %s constant %s is not defined',
                                (
                                    'namespacedName' === $key
                                    ? 'namespaced'
                                    : 'global'
                                ),
                                $this->getCaster()->cast($match[$key]),
                            ));
                        }

                        return constant($match[$key]);
                    }
                }

                if ($match['scopedName'] ?? false) {
                    switch ($match['scope'] ?? '') {
                        case 'parent':
                        case 'self':
                            $isParentTraversalAllowed = ($match['scope'] === ('parent'));

                            $reflectionClassCurrent = $reflectionParameter->getDeclaringClass();
                            $currentClassLevelIndex = -1;

                            while ($reflectionClassCurrent) {
                                $currentClassLevelIndex++;

                                if ($reflectionClassCurrent->hasConstant($match['scopedName'])) {
                                    $reflectionClassConstant = $reflectionClassCurrent->getReflectionConstant(
                                        $match['scopedName']
                                    );

                                    assert(is_object($reflectionClassConstant));
                                    assert($reflectionClassConstant instanceof ReflectionClassConstant);

                                    if ($reflectionClassConstant->isPrivate()) {
                                        if (0 === $currentClassLevelIndex) {
                                            return $reflectionClassConstant->getValue();
                                        }
                                    } else {
                                        return $reflectionClassConstant->getValue();
                                    }
                                }

                                if (false === $isParentTraversalAllowed) {
                                    break;
                                }

                                $reflectionClassCurrent = $reflectionClassCurrent->getParentClass();
                            }

                            $exceptionMessage = sprintf(
                                'Unable to locate the constant %s::%s',
                                $match['scope'],
                                $match['scopedName'],
                            );

                            if (
                                $isParentTraversalAllowed
                                && $reflectionParameter->getDeclaringClass()
                                && $reflectionParameter->getDeclaringClass()->getParentClass()
                            ) {
                                $exceptionMessage .= ' or at any parent class';
                            }

                            throw new RuntimeException($exceptionMessage);
                    }

                    /*
                    * Notice: The binding "static" is not allowed in compile-time constants.
                    */

                    throw new RuntimeException(sprintf(
                        'Uncovered case for $match[\'scope\'] = %s',
                        $this->getCaster()->castTyped($match['scope']),
                    ));
                }

                if ($match['classConstantName'] ?? false) {
                    // If class or interface does not exist, the PHP process itself will cause an Error
                    if (
                        false === class_exists($match['className'])
                        && false === interface_exists($match['className'])
                    ) {
                        throw new RuntimeException(sprintf(
                            'Class or interface %s does not exist, and therefore the constant reference %s is invalid',
                            $this->getCaster()->cast($match['className']),
                            $this->getCaster()->cast($match[0]),
                        ));
                    }

                    $reflectionClass = new ReflectionClass($match['className']);

                    if (false === $reflectionClass->hasConstant($match['classConstantName'])) {
                        throw new RuntimeException(sprintf(
                            '%s %s exists, but it does not have a constant named %s',
                            (
                                $reflectionClass->isInterface()
                                ? 'Interface'
                                : 'Class'
                            ),
                            $this->getCaster()->cast($match['className']),
                            $this->getCaster()->cast($match['classConstantName']),
                        ));
                    }

                    return $reflectionClass->getConstant($match['classConstantName']);
                }

                throw new RuntimeException(sprintf(
                    'Uncovered case for constant name %s and $match = %s',
                    $this->getCaster()->cast($defaultValueConstantName),
                    $this->getCaster()->castTyped($match),
                ));
            } catch (Throwable $t) {
                $functionText = '';

                if ($reflectionParameter->getDeclaringClass()) {
                    $isStatic = false;

                    if ($reflectionParameter->getDeclaringFunction() instanceof ReflectionMethod) {
                        $isStatic = $reflectionParameter->getDeclaringFunction()->isStatic();
                    }

                    $functionText = sprintf(
                        'method %s%s%s',
                        Caster::makeNormalizedClassName($reflectionParameter->getDeclaringClass()),
                        (
                            $isStatic
                            ? '::'
                            : '->'
                        ),
                        $reflectionParameter->getDeclaringFunction()->getName(),
                    );
                } else {
                    $functionText = sprintf(
                        'function \\%s',
                        $reflectionParameter->getDeclaringFunction()->getName(),
                    );
                }

                throw new RuntimeException(sprintf(
                    implode('', [
                        'Parameter $%s in %s has a default value, which is a constant, but a problem with this',
                        ' constant was encountered',
                    ]),
                    $reflectionParameter->getName(),
                    $functionText,
                ), 0, $t);
            }
        }

        return $reflectionParameter->getDefaultValue();
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getFunctionArgumentValues(): array
    {
        return $this->functionArgumentValues;
    }

    public function getFunctionArgumentValuesCount(): int
    {
        return count($this->functionArgumentValues);
    }

    public function getNamedParameterCount(): int
    {
        return $this->getReflectionFunction()->getNumberOfParameters();
    }

    /**
     * Function `func_get_args` will not include parameters with default values when those parameters utilize their
     * default values, i.e. no value has been passed on the respective parameter(s) with default values. We need to
     * account for this. Therefore, we do two things:
     *   1. We append default values for named parameters, which have default values, but haven't been called.
     *   2. We convert a potential variadic named parameter from all variadic array elements being at the same level as
     *      all other arguments, to being an array at the index position of the variadic named parameter.
     *
     * @return array<int, mixed>
     *
     * @throws RuntimeException
     */
    public function getNormalizedFunctionArgumentValues(): array
    {
        if (null === $this->normalizedFunctionArgumentValues) {
            $this->normalizedFunctionArgumentValues = [];
            $functionArgumentValues = array_values($this->getFunctionArgumentValues());
            $indexMax = (
                max(
                    count($functionArgumentValues),
                    $this->getNamedParameterCount(),
                )
                - 1
            );
            $indexLastNamedParameter = $this->getNamedParameterCount() - 1;

            for ($index = 0; $index <= $indexMax; $index++) {
                if ($index <= $indexLastNamedParameter) {
                    $reflectionParameter = $this->getReflectionParameterByIndex($index);

                    assert(is_object($reflectionParameter));

                    if ($index === $indexLastNamedParameter) {
                        if ($reflectionParameter->isVariadic()) {
                            $this->normalizedFunctionArgumentValues[$index] = array_slice(
                                $functionArgumentValues,
                                $index,
                            );

                            break;
                        }
                    }

                    $this->normalizedFunctionArgumentValues[$index] = null;

                    if (array_key_exists($index, $functionArgumentValues)) {
                        $this->normalizedFunctionArgumentValues[$index] = $functionArgumentValues[$index];
                    } elseif ($reflectionParameter->isDefaultValueAvailable()) {
                        $this->normalizedFunctionArgumentValues[$index] = $this->getDefaultValueForReflectionParameter(
                            $reflectionParameter
                        );
                    }
                } else {
                    $this->normalizedFunctionArgumentValues[$index] = $functionArgumentValues[$index];
                }
            }
        }

        return $this->normalizedFunctionArgumentValues;
    }

    public function getNormalizedFunctionArgumentValuesCount(): int
    {
        return count($this->getNormalizedFunctionArgumentValues());
    }

    public function getOptionalParameterCount(): int
    {
        return $this->getNamedParameterCount() - $this->getReflectionFunction()->getNumberOfRequiredParameters();
    }

    public function getLastNamedParameterIndex(): int
    {
        return $this->getNamedParameterCount() - 1;
    }

    public function getReflectionFunction(): ReflectionFunction|ReflectionMethod
    {
        return $this->reflectionFunction;
    }

    public function getReflectionParameterByIndex(int $index): ?ReflectionParameter
    {
        return ($this->getReflectionFunction()->getParameters()[$index] ?? null);
    }

    public function isLastNamedParameterVariadic(): bool
    {
        $lastNamedParameterIndex = $this->getLastNamedParameterIndex();

        return (
            array_key_exists($lastNamedParameterIndex, $this->getReflectionFunction()->getParameters())
            && $this->getReflectionFunction()->getParameters()[$lastNamedParameterIndex] instanceof ReflectionParameter
            && $this->getReflectionFunction()->getParameters()[$lastNamedParameterIndex]->isVariadic()
        );
    }
}
